#!/bin/sh
set -e

cd /var/www/app

# ------------------------------------------------------------------
# Railway MySQL service exposes: MYSQLHOST, MYSQLPORT, MYSQLUSER,
# MYSQLPASSWORD, MYSQLDATABASE.
# If the user set DB_HOST etc. via reference vars those take priority.
# Fall back to the native MYSQL* vars if DB_* are missing/localhost.
# ------------------------------------------------------------------
RESOLVED_DB_HOST="${DB_HOST:-${MYSQLHOST:-127.0.0.1}}"
RESOLVED_DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
RESOLVED_DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-railway}}"
RESOLVED_DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-root}}"
RESOLVED_DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"

# Write a fresh .env with all resolved values
cat > .env <<EOF
APP_NAME="${APP_NAME:-OrderList}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${RESOLVED_DB_HOST}"
DB_PORT="${RESOLVED_DB_PORT}"
DB_DATABASE="${RESOLVED_DB_DATABASE}"
DB_USERNAME="${RESOLVED_DB_USERNAME}"
DB_PASSWORD="${RESOLVED_DB_PASSWORD}"

SESSION_DRIVER="${SESSION_DRIVER:-file}"
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK="${FILESYSTEM_DISK:-local}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
CACHE_STORE="${CACHE_STORE:-file}"

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="OrderList"
EOF

echo "DB config: host=${RESOLVED_DB_HOST} port=${RESOLVED_DB_PORT} db=${RESOLVED_DB_DATABASE} user=${RESOLVED_DB_USERNAME}"

# Generate app key if not provided
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear stale caches
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

# Cache for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g 'daemon off;'
