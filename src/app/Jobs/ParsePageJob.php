<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Review;
use App\Models\Settings;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ParsePageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $timeout = 6000;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    private $jobId;
    private $totalReviews = 0;
    private $processedReviews = 0;

    public function handle()
    {
        $settings = Settings::getSettings();
        
        if (empty($settings->org_link)) 
        {
            Log::warning('Ссылка на организацию не указана');
            return;
        }

        // Генерируем уникальный ID задания
        $this->jobId = uniqid('parse_', true);
        
        // Отправляем в Redis очередь
        Redis::rpush('yandex:parse:queue', json_encode([
            'job_id' => $this->jobId,
            'org_link' => $settings->org_link,
        ]));

        Log::info('Задание отправлено Puppeteer', ['job_id' => $this->jobId]);

        // Запускаем мониторинг прогресса
        $this->monitorProgress();
    }

    protected function monitorProgress($timeout = 6000)
    {
        $start = time();
        $lastPartialId = null;
        
        while (time() - $start < $timeout) 
        {
            // Проверяем метаданные (общее количество)
            $meta = Redis::get("yandex:meta:{$this->jobId}");
            if ($meta && !$this->totalReviews) 
            {
                $metaData = json_decode($meta, true);
                $this->totalReviews = $metaData['totalReviews'] ?? 0;
                
                // Сразу обновляем настройки с общим количеством
                Settings::updateProgress([
                    'total_reviews' => $this->totalReviews,
                    'total_rating' => $metaData['totalRating'] ?? 0,
                    'last_sync_status' => "Начинаем сбор... 0/{$this->totalReviews}",
                ]);
                
                Log::info('Получены метаданные', [
                    'total_reviews' => $this->totalReviews,
                    'rating' => $metaData['totalRating'] ?? 0
                ]);
            }

            // Проверяем прогресс
            $progress = Redis::get("yandex:progress:{$this->jobId}");
            if ($progress) 
            {
                $progressData = json_decode($progress, true);
                $this->processedReviews = $progressData['current'] ?? 0;
                
                // Обновляем прогресс в настройках
                Settings::updateProgress([
                    'processed_reviews' => $this->processedReviews,
                    'last_sync_status' => "Собрано {$this->processedReviews}/{$this->totalReviews} отзывов",
                    'progress_percent' => $this->totalReviews > 0 
                        ? round(($this->processedReviews / $this->totalReviews) * 100) 
                        : 0,
                ]);
            }

            // Проверяем частичные результаты
            $partial = Redis::get("yandex:partial:{$this->jobId}");
            if ($partial) 
            {
                $partialData = json_decode($partial, true);
                
                // Сохраняем новые отзывы (если они есть)
                if (!empty($partialData['reviews'])) 
                {
                    $this->saveReviews($partialData['reviews']);
                }
            }

            // Проверяем финальный результат
            $result = Redis::get("yandex:result:{$this->jobId}");
            if ($result) 
            {
                $data = json_decode($result, true);
                
                if ($data['success']) 
                {
                    // Сохраняем оставшиеся отзывы (на всякий случай)
                    if (!empty($data['reviews'])) 
                    {
                        $this->saveReviews($data['reviews']);
                    }
                    
                    // Обновляем финальные настройки
                    Settings::updateProgress([
                        'last_sync' => now(),
                        'reviews_count' => Review::count(),
                        'last_sync_status' => "✅ Успешно: " . count($data['reviews']) . " отзывов",
                        'last_sync_duration' => $data['duration'] ?? null,
                        'total_reviews' => $data['meta']['totalReviews'] ?? $this->totalReviews,
                        'total_rating' => $data['meta']['totalRating'] ?? 0,
                        'progress_percent' => 100,
                    ]);
                    
                    // Очищаем временные ключи
                    $this->cleanupRedisKeys();
                    
                    Log::info('Парсинг завершен', [
                        'count' => count($data['reviews']),
                        'duration' => $data['duration'] ?? '?'
                    ]);
                    
                    return;
                }
            }

            // Если задание выполняется долго, но нет прогресса - проверяем живость
            if (time() - $start > 60 && !$progress && !$partial) 
            {
                Log::warning('Нет прогресса в течение минуты', ['job_id' => $this->jobId]);
            }

            sleep(3); // Проверяем каждые 3 секунды
        }
        
        // Таймаут - задание зависло
        Log::error('Таймаут ожидания результата', ['job_id' => $this->jobId]);
        Settings::updateProgress([
            'last_sync_status' => "❌ Таймаут после {$timeout}с",
        ]);
        
        $this->cleanupRedisKeys();
    }

    protected function saveReviews($reviews)
    {
        foreach ($reviews as $reviewData) 
        {
            try 
            {
                Review::updateOrCreate(
                    ['review_id' => $reviewData['review_id']],
                    [
                        'author_name' => $reviewData['author'],
                        'rating' => $reviewData['rating'],
                        'text' => $reviewData['text'] ?? '',
                        'review_created_at' => $this->parseDate($reviewData['date']),
                    ]
                );
            } 
            catch (\Exception $e) 
            {
                Log::error('Ошибка сохранения отзыва', [
                    'review_id' => $reviewData['review_id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) 
        {
            return now();
        }
        
        try
        {
            return \Carbon\Carbon::parse($dateString);
        } 
        catch (\Exception $e) 
        {
            return now();
        }
    }

    protected function cleanupRedisKeys()
    {
        $keys = [
            "yandex:meta:{$this->jobId}",
            "yandex:progress:{$this->jobId}",
            "yandex:partial:{$this->jobId}",
            "yandex:result:{$this->jobId}"
        ];
        
        foreach ($keys as $key) 
        {
            Redis::del($key);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('ParsePageJob провалился', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage()
        ]);
        
        Settings::updateProgress([
            'last_sync_status' => "❌ Ошибка: " . $exception->getMessage(),
        ]);
        
        $this->cleanupRedisKeys();
    }
}