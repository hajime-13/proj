# syntax=docker/dockerfile:1

FROM php:8.2-fpm-alpine

# Install system deps + supervisord
RUN apk add --no-cache \
    nginx \
    bash \
    netcat-openbsd \
    supervisor \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    sqlite-dev \
    zip unzip \
    git \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite mbstring zip opcache gd \
    && docker-php-ext-enable opcache

WORKDIR /var/www/app

# Copy configs
COPY ./docker/nginx.conf      /etc/nginx/nginx.conf
COPY ./docker/php-fpm.conf    /etc/php-fpm-railway.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Copy app
COPY . /var/www/app

# Bootstrap .env for build-time artisan commands
RUN cp .env.example .env

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Generate placeholder key
RUN php artisan key:generate --force

# Build assets
RUN npm install --no-fund --no-audit && npm run build

# Permissions
RUN mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache .env

# Startup script
COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
