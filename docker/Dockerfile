FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    nodejs npm redis-server supervisor nginx wget gnupg2 \
    net-tools lsof \
    && rm -rf /var/lib/apt/lists/*

RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | gpg --dearmor -o /usr/share/keyrings/google-chrome.gpg \
    && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-chrome.gpg] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update && apt-get install -y google-chrome-stable \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && wget -O /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/6.1.0.tar.gz \
    && tar xfz /tmp/redis.tar.gz -C /tmp \
    && rm -f /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext/redis \
    && mv /tmp/phpredis-6.1.0/* /usr/src/php/ext/redis/ \
    && docker-php-ext-install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN npm install -g puppeteer ioredis

WORKDIR /var/www

COPY src/ .

RUN mkdir -p /var/log/supervisor /var/run/php \
    && chmod -R 777 /var/log/supervisor /var/run/php

RUN cp .env.example .env \
    && composer install --no-interaction --optimize-autoloader \
    && php artisan key:generate \
    && npm install \
    && php artisan ziggy:generate \
    && npm run build \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD sh -c "cp -f /var/www/.env.example /var/www/.env && php artisan key:generate && php artisan ziggy:generate && npm run build && /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf"