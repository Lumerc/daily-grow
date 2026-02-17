#!/bin/sh
set -x  # Включаем подробный вывод

echo "🚀 Entrypoint started at $(date)"
exec > /proc/1/fd/1 2>/proc/1/fd/2
#exec > /var/log/entrypoint.log 2>&1

# 1. СОЗДАЁМ .env, ЕСЛИ ЕГО НЕТ
echo "⚠️ .env not found, creating from example..."
cp /var/www/.env.example /var/www/.env
echo "✅ .env created"

# 5. СОЗДАЁМ НЕОБХОДИМЫЕ ПАПКИ И СТАВИМ ПРАВА
mkdir -p /var/www/bootstrap/cache
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions

chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage


composer install --no-interaction --optimize-autoloader

npm cache clean --force && rm -rf node_modules && rm -rf package-lock.json

# Фронтенд
npm install

npm install puppeteer-core --unsafe-perm
php artisan ziggy:generate 
npm run build

# 2. ГЕНЕРИРУЕМ APP_KEY, ЕСЛИ ОН ОТСУТСТВУЕТ

echo "⚠️ APP_KEY not found, generating..."
php /var/www/artisan key:generate
echo "✅ APP_KEY generated"


# 4. УСТАНАВЛИВАЕМ NPM-ЗАВИСИМОСТИ И СОБИРАЕМ ФРОНТ, ЕСЛИ НЕТ МАНИФЕСТА

php artisan migrate --seed

php artisan config:clear
php artisan cache:clear
php artisan view:clear


# 6. ЗАПУСКАЕМ SUPERVISOR
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf

