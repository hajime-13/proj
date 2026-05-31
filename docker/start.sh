#!/bin/sh
set -e

cd /var/www/app

# ------------------------------------------------------------------
# Resolve DB credentials — Railway MySQL exposes MYSQLHOST etc.
# ------------------------------------------------------------------
RESOLVED_DB_HOST="${DB_HOST:-${MYSQLHOST:-127.0.0.1}}"
RESOLVED_DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
RESOLVED_DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-railway}}"
RESOLVED_DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-root}}"
RESOLVED_DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"

echo "DB: host=${RESOLVED_DB_HOST} port=${RESOLVED_DB_PORT} db=${RESOLVED_DB_DATABASE}"

# Write fresh .env
cat > /var/www/app/.env <<EOF
APP_NAME="${APP_NAME:-OrderList}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=mysql
DB_HOST=${RESOLVED_DB_HOST}
DB_PORT=${RESOLVED_DB_PORT}
DB_DATABASE=${RESOLVED_DB_DATABASE}
DB_USERNAME=${RESOLVED_DB_USERNAME}
DB_PASSWORD=${RESOLVED_DB_PASSWORD}

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
EOF

# Fix ownership so www-data can read .env
chown www-data:www-data /var/www/app/.env

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear stale build-time caches
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true

# Run migrations
php artisan migrate --force

# Storage symlink
php artisan storage:link || true

# Re-cache with correct runtime values
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ------------------------------------------------------------------
# Start PHP-FPM in foreground as background process, then wait
# for port 9000 to be ready before starting Nginx
# ------------------------------------------------------------------
php-fpm --nodaemonize &
PHP_FPM_PID=$!

echo "Waiting for PHP-FPM on port 9000..."
for i in $(seq 1 30); do
    if nc -z 127.0.0.1 9000 2>/dev/null; then
        echo "PHP-FPM is ready."
        break
    fi
    sleep 1
done

# Start Nginx in foreground (keeps container alive)
exec nginx -g 'daemon off;'
