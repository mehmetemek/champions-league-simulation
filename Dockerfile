FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    libzip-dev \
    git \
    oniguruma-dev

RUN docker-php-ext-install pdo_mysql mbstring opcache zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

EXPOSE 9000
CMD ["php-fpm"]