#!/bin/sh
set -e

cd /var/www/app

echo "=== ENV CHECK ==="
echo "DB_HOST=[${DB_HOST}]"
echo "DB_PORT=[${DB_PORT}]"
echo "DB_DATABASE=[${DB_DATABASE}]"
echo "DB_USERNAME=[${DB_USERNAME}]"
echo "APP_URL=[${APP_URL}]"
echo "================="

# Sanitize APP_URL
_APP_URL="${APP_URL}"
case "$_APP_URL" in
    http://*|https://*) ;;
    *) _APP_URL="http://localhost" ;;
esac

# Write .env
cat > /var/www/app/.env << ENVEOF
APP_NAME=OrderList
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${_APP_URL}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-railway}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=OrderList
ENVEOF

chown www-data:www-data /var/www/app/.env
echo "DB_HOST in .env: $(grep ^DB_HOST /var/www/app/.env)"

# Generate key if missing
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear caches
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true
php artisan cache:clear  || true

# Migrate
php artisan migrate --force || echo "WARNING: migrate failed"

# Storage symlink
php artisan storage:link || true

# Hand off to supervisord (manages php-fpm + nginx)
exec supervisord -c /etc/supervisord.conf
