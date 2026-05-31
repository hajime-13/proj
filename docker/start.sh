#!/bin/sh
set -e

cd /var/www/app

# -------------------------------------------------------
# Inject Railway environment variables into .env
# Railway passes DB_*, APP_*, etc. as real env vars.
# We write them into .env so Laravel's config system
# picks them up correctly (especially after config:cache).
# -------------------------------------------------------
inject_env() {
    KEY="$1"
    VALUE="$2"
    if [ -n "$VALUE" ]; then
        # Replace existing key or append
        if grep -q "^${KEY}=" .env 2>/dev/null; then
            sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" .env
        else
            echo "${KEY}=${VALUE}" >> .env
        fi
    fi
}

inject_env APP_KEY         "$APP_KEY"
inject_env APP_ENV         "$APP_ENV"
inject_env APP_DEBUG       "$APP_DEBUG"
inject_env APP_URL         "$APP_URL"
inject_env DB_CONNECTION   "$DB_CONNECTION"
inject_env DB_HOST         "$DB_HOST"
inject_env DB_PORT         "$DB_PORT"
inject_env DB_DATABASE     "$DB_DATABASE"
inject_env DB_USERNAME     "$DB_USERNAME"
inject_env DB_PASSWORD     "$DB_PASSWORD"
inject_env SESSION_DRIVER  "$SESSION_DRIVER"
inject_env CACHE_STORE     "$CACHE_STORE"
inject_env QUEUE_CONNECTION "$QUEUE_CONNECTION"
inject_env FILESYSTEM_DISK "$FILESYSTEM_DISK"

# Generate app key if still missing
php artisan key:generate --force

# Clear any stale caches from build time
php artisan config:clear  || true
php artisan route:clear   || true
php artisan view:clear    || true

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
