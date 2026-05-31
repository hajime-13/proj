# syntax=docker/dockerfile:1

FROM php:8.2-fpm-alpine

# Install system deps
RUN apk add --no-cache \
    nginx \
    bash \
    netcat-openbsd \
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

# Nginx + PHP-FPM config
RUN rm -rf /var/www/html && mkdir -p /var/www/app
WORKDIR /var/www/app

# Copy Nginx config
COPY ./docker/nginx.conf /etc/nginx/nginx.conf

# Copy app code
COPY . /var/www/app

# Create .env from example so artisan commands work at build & runtime
RUN cp .env.example .env

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP deps (prod, no dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Generate a placeholder key (will be overwritten at runtime by start.sh)
RUN php artisan key:generate --force

# Build front-end assets
RUN npm install --no-fund --no-audit && npm run build

# Laravel permissions
RUN mkdir -p /var/www/app/storage/app/public \
             /var/www/app/storage/framework/cache \
             /var/www/app/storage/framework/sessions \
             /var/www/app/storage/framework/views \
             /var/www/app/storage/logs \
             /var/www/app/bootstrap/cache \
    && chown -R www-data:www-data /var/www/app/storage /var/www/app/bootstrap/cache \
    && chown www-data:www-data /var/www/app/.env

# Override PHP-FPM pool to listen on TCP 9000 and run as www-data
RUN echo '[www]' > /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'user = www-data' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'group = www-data' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'pm.max_children = 10' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.d/zz-railway.conf \
 && echo 'pm.max_spare_servers = 3' >> /usr/local/etc/php-fpm.d/zz-railway.conf

# Copy startup script
COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
