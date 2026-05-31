# syntax=docker/dockerfile:1

FROM php:8.2-fpm-alpine

# Install system deps
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

# Force PHP-FPM to listen on TCP 9000 (override default pool)
RUN { \
    echo '[www]'; \
    echo 'listen = 127.0.0.1:9000'; \
    echo 'user = www-data'; \
    echo 'group = www-data'; \
    echo 'pm = dynamic'; \
    echo 'pm.max_children = 10'; \
    echo 'pm.start_servers = 2'; \
    echo 'pm.min_spare_servers = 1'; \
    echo 'pm.max_spare_servers = 3'; \
} > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/app

COPY ./docker/nginx.conf /etc/nginx/nginx.conf

COPY . /var/www/app

RUN cp .env.example .env

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

RUN php artisan key:generate --force

RUN npm install --no-fund --no-audit && npm run build

RUN mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache .env

COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
