const puppeteer = require('puppeteer');
const Redis = require('ioredis');

const redis = new Redis({ 
    host: 'redis',
    port: 6379,
    retryStrategy: times => Math.min(times * 50, 2000)
});

redis.ping().then(pong => {
    console.log('✅ Redis connection OK:', pong);
}).catch(err => {
    console.error('❌ Redis connection FAILED:', err);
});

console.log('🤖 Puppeteer+AJAX worker started, waiting for jobs...');

async function parseYandexReviews(job) {
    let browser = null;
    
    try {
        browser = await puppeteer.launch({
            headless: 'new',
            executablePath: '/usr/bin/google-chrome-stable',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-blink-features=AutomationControlled',
                '--window-size=1920,1080',
                '--disable-gpu',
                '--disable-software-rasterizer',
                '--disable-dev-shm-usage',
                '--no-first-run',
                '--no-default-browser-check'
            ]
        });

        const page = await browser.newPage();
        
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        
        await page.evaluateOnNewDocument(() => {
            delete navigator.__proto__.webdriver;
            window.chrome = { runtime: {} };
        });

        page.on('console', msg => {
            console.log('🖥️ [Браузер]:', msg.text());
        });

        page.on('pageerror', error => {
            console.error('❌ [Ошибка браузера]:', error.message);
        });

        console.log('🌐 Заходим на страницу...');
        await page.goto(job.org_link, { 
            waitUntil: 'networkidle2', 
            timeout: 60000 
        });

        await new Promise(r => setTimeout(r, 3000));

        console.log('🔍 Извлекаем параметры и аспекты...');
        
        const result = await page.evaluate(async () => {
            function djb2Hash(e) {
                let n = 5381;
                for (let i = 0; i < e.length; i++) {
                    n = (33 * n) ^ e.charCodeAt(i);
                }
                return n >>> 0;
            }

            // === ИЗВЛЕКАЕМ АСПЕКТЫ ИЗ HTML ===
            function extractAspects() 
            {
                // 1. Получаем названия из DOM
                const names = [];
                const elements = document.querySelectorAll('.business-aspect-view');
                elements.forEach(el => {
                    const textElement = el.querySelector('.business-aspect-view__text');
                    const countElement = el.querySelector('.business-aspect-view__count');
                    
                    if (textElement && countElement) {
                        const fullText = textElement.childNodes[0]?.nodeValue || '';
                        const name = fullText.replace('•', '').trim().replace(/\.$/, '');
                        
                        const countText = countElement.textContent;
                        const countMatch = countText.match(/(\d+)/);
                        const count = countMatch ? parseInt(countMatch[1]) : 0;
                        
                        names.push({ name, count });
                    }
                });

                // 2. Собираем все пары id/text из HTML
                const html = document.documentElement.innerHTML;
                const idTextPairs = [];
                const pairRegex = /"id":"(\d+)".*?"text":"([^"]+)"/g;
                let pairMatch;

                while ((pairMatch = pairRegex.exec(html)) !== null) {
                    idTextPairs.push({
                        id: pairMatch[1],
                        text: pairMatch[2]
                    });
                }

                // 3. Сопоставляем
                const aspects = [];
                names.forEach(item => {
                    const found = idTextPairs.find(p => p.text === item.name);
                    if (found) {
                        aspects.push({
                            id: found.id,
                            text: item.name,
                            count: item.count
                        });
                    }
                });

                return aspects;
            }

            // === ПОЛУЧАЕМ АСПЕКТЫ ===
            const aspects = extractAspects();
            return aspects;
        });

        const aspects = result;
        console.log(`📊 Всего аспектов: ${aspects.length}`);
        
        if (aspects.length > 0) {
            aspects.forEach(a => {
                console.log(`   - ${a.text} (${a.id}): ${a.count} отзывов`);
            });
        }

        // Получаем общее количество отзывов и рейтинг
        const meta = await page.evaluate(() => {
            const ratingMeta = document.querySelector('meta[itemProp="ratingValue"]');
            const countMeta = document.querySelector('meta[itemProp="reviewCount"]');
            
            return {
                totalRating: ratingMeta ? parseFloat(ratingMeta.getAttribute('content')) : 0,
                totalReviews: countMeta ? parseInt(countMeta.getAttribute('content')) : 0
            };
        });

        console.log(`📊 Всего отзывов: ${meta.totalReviews}, Рейтинг: ${meta.totalRating}`);

        // Отправляем метаданные сразу в Redis
        await redis.setex(
            `yandex:meta:${job.job_id}`,
            3600,
            JSON.stringify({
                totalRating: meta.totalRating,
                totalReviews: meta.totalReviews,
                aspects: aspects.map(a => ({
                    id: a.id,
                    name: a.text,
                    count: a.count
                }))
            })
        );

        // Получаем параметры для запросов
        const params = await page.evaluate(() => {
            const html = document.documentElement.innerHTML;
            const businessId = window.location.pathname.match(/org\/[^\/]+\/(\d+)\//)?.[1];
            const csrfMatch = html.match(/"csrfToken":"([^"]+)"/);
            const localeMatch = html.match(/"locale":"([^"]+)"/);
            const reqMatch = html.match(/"requestSerpId":"([^"]+)"/);
            const sessionMatch = html.match(/"sessionId":"([^"]+)"/);

            return {
                businessId,
                csrfToken: csrfMatch ? csrfMatch[1] : '',
                locale: localeMatch ? localeMatch[1] : 'ru_US',
                reqId: reqMatch ? reqMatch[1] : '',
                sessionId: sessionMatch ? sessionMatch[1] : ''
            };
        });

        if (!params.businessId || !params.csrfToken || !params.reqId || !params.sessionId) {
            console.error('❌ Не удалось извлечь параметры');
            return { reviews: [], meta: { totalRating: 0, totalReviews: 0, aspects: [] } };
        }

        // Функция для выполнения одного запроса (с aspectId или без)
        async function fetchPage(aspectId, pageNum, ranking) {
            return await page.evaluate(async (aid, p, rank, bizId, token, loc, rId, sId) => {
                function djb2Hash(e) {
                    let n = 5381;
                    for (let i = 0; i < e.length; i++) {
                        n = (33 * n) ^ e.charCodeAt(i);
                    }
                    return n >>> 0;
                }

                // Формируем строку для хеша (с aspectId если есть)
                let stringForHash;
                if (aid) {
                    stringForHash = `ajax=1&aspectId=${aid}&businessId=${bizId}&csrfToken=${token}&locale=${loc}&page=${p}&pageSize=50&ranking=${rank}&reqId=${rId}&sessionId=${sId}`.replace(':', '%3A');
                } else {
                    stringForHash = `ajax=1&businessId=${bizId}&csrfToken=${token}&locale=${loc}&page=${p}&pageSize=50&ranking=${rank}&reqId=${rId}&sessionId=${sId}`.replace(':', '%3A');
                }
                
                const s = djb2Hash(stringForHash);

                // Формируем URL (с aspectId если есть)
                const urlParts = [
                    `ajax=1`,
                    `businessId=${bizId}`,
                    `csrfToken=${token.replace(':', '%3A')}`,
                    `locale=${loc}`,
                    `page=${p}`,
                    `pageSize=50`,
                    `ranking=${rank}`,
                    `reqId=${rId}`,
                    `s=${s}`,
                    `sessionId=${sId}`
                ];
                
                if (aid) {
                    urlParts.splice(1, 0, `aspectId=${aid}`);
                }

                const url = '/maps/api/business/fetchReviews?' + urlParts.join('&');

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                if (!response.ok) return [];

                const data = await response.json();
                if (!data.data || !data.data.reviews) return [];

                const reviews = [];
                for (const item of data.data.reviews) {
                    const rating = item.rating || 0;
                    if (rating === 0) continue;

                    reviews.push({
                        author: item.author?.name || 'Аноним',
                        rating: rating,
                        text: item.text || '',
                        date: item.updatedTime || '',
                        review_id: item.reviewId || `rev_${Date.now()}_${Math.random()}`,
                    });
                }

                return reviews;
            }, aspectId, pageNum, ranking, params.businessId, params.csrfToken, params.locale, params.reqId, params.sessionId);
        }

        // === НОВОЕ: ОБЩИЕ РАНКИНГИ ===
        const generalRankings = [
            { name: 'relevance', value: 'by_relevance_org' },
            { name: 'time', value: 'by_time' },
            { name: 'rating_asc', value: 'by_rating_asc' },
            { name: 'rating_desc', value: 'by_rating_desc' }
        ];

        // === СБОР ОТЗЫВОВ ===
        let allReviews = [];
        let totalPages = 0;
        
        // Считаем общее количество страниц (аспекты + общие)
        aspects.forEach(aspect => {
            const total = aspect.count || 0;
            if (total <= 600) {
                totalPages += Math.ceil(total / 50);
            } else {
                totalPages += Math.ceil(600 / 50);
                totalPages += Math.ceil(600 / 50);
            }
        });
        
        // Добавляем страницы от общих ранкингов (максимум 600 отзывов)
        const generalPagesPerRanking = meta.totalReviews <= 600 
            ? Math.ceil(meta.totalReviews / 50)
            : Math.ceil(600 / 50);
        totalPages += generalPagesPerRanking * generalRankings.length;

        console.log(`📋 Всего страниц для сбора: ${totalPages}`);

        let currentPage = 0;
        const reviewIds = new Set(); // Для дедупликации

        // 1. Сначала собираем по аспектам
        for (const aspect of aspects) {
            const aspectId = aspect.id;
            const aspectName = aspect.text;
            const total = aspect.count || 0;

            console.log(`\n🔍 Аспект: ${aspectName} (${aspectId}), всего: ${total}`);

            let pages = [];

            if (total <= 600) {
                const pageCount = Math.ceil(total / 50);
                for (let i = 1; i <= pageCount; i++) {
                    pages.push({ aspectId, page: i, ranking: 'by_aspect_tone_asc' });
                }
            } else {
                const negativePages = Math.ceil(600 / 50);
                for (let i = 1; i <= negativePages; i++) {
                    pages.push({ aspectId, page: i, ranking: 'by_aspect_tone_asc' });
                }
                const positivePages = Math.ceil(600 / 50);
                for (let i = 1; i <= positivePages; i++) {
                    pages.push({ aspectId, page: i, ranking: 'by_aspect_tone_desc' });
                }
            }

            for (const p of pages) {
                currentPage++;
                console.log(`   📄 Запрос ${currentPage}/${totalPages}: аспект ${aspectName}, page=${p.page}, ranking=${p.ranking}`);
                
                const reviews = await fetchPage(p.aspectId, p.page, p.ranking);
                
                // Дедупликация
                for (const review of reviews) {
                    if (!reviewIds.has(review.review_id)) {
                        reviewIds.add(review.review_id);
                        allReviews.push(review);
                    }
                }

                console.log(`      ✅ Получено: ${reviews.length} отзывов, уникальных: ${allReviews.length}`);

                // Обновляем прогресс
                await redis.setex(
                    `yandex:progress:${job.job_id}`,
                    3600,
                    JSON.stringify({
                        current: allReviews.length,
                        total: meta.totalReviews,
                        pages: {
                            current: currentPage,
                            total: totalPages
                        },
                        aspect: aspectName,
                        lastUpdate: Date.now()
                    })
                );

                // Отправляем промежуточные результаты
                await redis.setex(
                    `yandex:partial:${job.job_id}`,
                    3600,
                    JSON.stringify({
                        reviews: reviews,
                        progress: {
                            current: allReviews.length,
                            total: meta.totalReviews
                        }
                    })
                );

                // Пауза между запросами
                if (currentPage < totalPages) {
                    console.log('      ⏳ Ожидание 15 секунд...');
                    await new Promise(r => setTimeout(r, 15000));
                }
            }
        }

        // 2. Теперь собираем по общим ранкингам
        console.log(`\n🔍 Сбор по общим ранкингам...`);

        for (const ranking of generalRankings) {
            console.log(`\n📊 Ранкинг: ${ranking.name} (${ranking.value})`);

            const pagesCount = meta.totalReviews <= 600 
                ? Math.ceil(meta.totalReviews / 50)
                : Math.ceil(600 / 50);

            for (let page = 1; page <= pagesCount; page++) {
                currentPage++;
                console.log(`   📄 Запрос ${currentPage}/${totalPages}: ранкинг ${ranking.name}, page=${page}`);
                
                const reviews = await fetchPage(null, page, ranking.value);
                
                // Дедупликация
                for (const review of reviews) {
                    if (!reviewIds.has(review.review_id)) {
                        reviewIds.add(review.review_id);
                        allReviews.push(review);
                    }
                }

                console.log(`      ✅ Получено: ${reviews.length} отзывов, уникальных: ${allReviews.length}`);

                // Обновляем прогресс
                await redis.setex(
                    `yandex:progress:${job.job_id}`,
                    3600,
                    JSON.stringify({
                        current: allReviews.length,
                        total: meta.totalReviews,
                        pages: {
                            current: currentPage,
                            total: totalPages
                        },
                        ranking: ranking.name,
                        lastUpdate: Date.now()
                    })
                );

                // Отправляем промежуточные результаты
                await redis.setex(
                    `yandex:partial:${job.job_id}`,
                    3600,
                    JSON.stringify({
                        reviews: reviews,
                        progress: {
                            current: allReviews.length,
                            total: meta.totalReviews
                        }
                    })
                );

                // Пауза между запросами
                if (currentPage < totalPages) {
                    console.log('      ⏳ Ожидание 15 секунд...');
                    await new Promise(r => setTimeout(r, 15000));
                }
            }
        }

        console.log(`\n✅ Всего собрано уникальных отзывов: ${allReviews.length} из ${meta.totalReviews}`);

        return {
            reviews: allReviews,
            meta: {
                totalRating: meta.totalRating,
                totalReviews: meta.totalReviews,
                aspects: aspects.map(a => ({
                    id: a.id,
                    name: a.text,
                    count: a.count
                }))
            }
        };

    } catch (error) {
        console.error('❌ Puppeteer error:', error);
        return { reviews: [], meta: { totalRating: 0, totalReviews: 0, aspects: [] } };
    } finally {
        if (browser) await browser.close();
    }
}

async function startWorker() {
    while (true) {
        try {
            const jobData = await redis.blpop('yandex:parse:queue', 0);
            
            if (jobData && jobData[1]) {
                const job = JSON.parse(jobData[1]);
                console.log(`\n📥 Получено задание: ${job.org_link}`);
                console.log(`⏱  Начало: ${new Date().toLocaleTimeString()}`);
                
                const startTime = Date.now();
                const result = await parseYandexReviews(job);
                const duration = ((Date.now() - startTime) / 1000).toFixed(1);
                
                // Сохраняем финальный результат
                await redis.setex(
                    `yandex:result:${job.job_id}`,
                    3600,
                    JSON.stringify({
                        success: true,
                        reviews: result.reviews,
                        meta: result.meta,
                        count: result.reviews.length,
                        job_id: job.job_id,
                        duration: duration
                    })
                );
                
                console.log(`✅ Готово за ${duration}с, отзывов: ${result.reviews.length}\n`);
            }
        } catch (error) {
            console.error('❌ Worker error:', error);
        }
    }
}

startWorker();