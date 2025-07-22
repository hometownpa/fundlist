# Use an official PHP-FPM image
FROM php:8.2-fpm-alpine

# Install system dependencies (like nginx) and necessary libraries for PHP extensions
RUN apk update && apk add --no-cache \
    nginx \
    build-base \
    autoconf \
    g++ \
    libzip-dev \
    icu-dev \
    git \
    zlib-dev \
    # REMOVED GD-SPECIFIC LIBRARIES: libpng-dev, libjpeg-turbo-dev, libwebp-dev, freetype-dev
    && rm -rf /var/cache/apk/*

# Install PHP extensions using docker-php-ext-install
# GD AND ITS CONFIGURATION REMOVED FOR DIAGNOSIS
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    opcache \
    intl \
    mbstring \
    xml \
    json \
    session \
    ctype \
    tokenizer \
    dom \
    curl \
    filter \
    hash \
    iconv \
    openssl \
    simplexml \
    xmlreader \
    xmlwriter \
    zlib

# Install APCu (it's a PECL extension, so it's installed differently)
RUN pecl install apcu && docker-php-ext-enable apcu

# Install Composer (if your project uses Composer dependencies)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
# RUN composer install --no-dev --optimize-autoloader

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy your PHP application code
COPY . /var/www/html/pch
WORKDIR /var/www/html/pch

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/pch
RUN find /var/www/html/pch -type d -exec chmod 755 {} +
RUN find /var/www/html/pch -type f -exec chmod 644 {} +

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD sh -c "php-fpm && nginx -g 'daemon off;'"