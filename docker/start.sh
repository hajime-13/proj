#!/bin/sh

cd /var/www/app

# ------------------------------------------------------------------
# DEBUG: show injected vars
# ------------------------------------------------------------------
echo "=== ENV CHECK ==="
echo "DB_HOST=[${DB_HOST}]"
echo "DB_PORT=[${DB_PORT}]"
echo "DB_DATABASE=[${DB_DATABASE}]"
echo "DB_USERNAME=[${DB_USERNAME}]"
echo "APP_URL=[${APP_URL}]"
echo "APP_KEY=[${APP_KEY:0:20}...]"
echo "================="

# ------------------------------------------------------------------
# Sanitize APP_URL — must be a valid http/https URL
# ------------------------------------------------------------------
_APP_URL="${APP_URL}"
case "$_APP_URL" in
    http://*|https://*) ;;  # valid
    *) _APP_URL="http://localhost" ;;  # fallback if malformed
esac

# ------------------------------------------------------------------
# Build .env
# ------------------------------------------------------------------
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

echo "Written .env — DB_HOST=$(grep DB_HOST /var/www/app/.env)"

# Generate key if missing
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear caches (do NOT re-cache — read .env directly at runtime)
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true
php artisan cache:clear  || true

# Run migrations — don't crash the container if this fails
php artisan migrate --force || echo "WARNING: migrate failed"

# Storage symlink
php artisan storage:link || true

# Start PHP-FPM in background
php-fpm --nodaemonize &

# Wait for PHP-FPM on port 9000
echo "Waiting for PHP-FPM..."
for i in $(seq 1 30); do
    nc -z 127.0.0.1 9000 2>/dev/null && echo "PHP-FPM ready." && break
    sleep 1
done

# Start Nginx in foreground
exec nginx -g 'daemon off;'
