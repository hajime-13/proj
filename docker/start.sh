#!/bin/sh
set -e

cd /var/www/app

# Write .env
_APP_URL="${APP_URL}"
case "$_APP_URL" in
    http://*|https://*) ;;
    *) _APP_URL="http://localhost" ;;
esac

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

[ -z "$APP_KEY" ] && php artisan key:generate --force

php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true
php artisan migrate --force || true
php artisan storage:link  || true

# Start PHP-FPM — use the bundled default config (TCP 9000)
# The zz-railway.conf in the image already sets listen=127.0.0.1:9000
php-fpm --nodaemonize &
FPM_PID=$!

# Wait up to 10s for PHP-FPM socket
echo "Waiting for PHP-FPM..."
i=0
while [ $i -lt 10 ]; do
    if kill -0 $FPM_PID 2>/dev/null && nc -z 127.0.0.1 9000 2>/dev/null; then
        echo "PHP-FPM up on :9000"
        break
    fi
    i=$((i+1))
    sleep 1
done

# Verify PHP-FPM is still alive
if ! kill -0 $FPM_PID 2>/dev/null; then
    echo "ERROR: PHP-FPM died. Check config."
    exit 1
fi

# Start nginx
exec nginx -g 'daemon off;'
