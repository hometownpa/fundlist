# Use an official PHP-FPM image with Nginx
# You can choose a different PHP version (e.g., 8.0-fpm-alpine, 8.3-fpm-alpine)
FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk update && apk add --no-cache \
    nginx \
    php82-pecl-apcu \
    php82-pdo_mysql \
    php82-mysqli \
    php82-zip \
    php82-gd \
    php82-opcache \
    php82-intl \
    php82-mbstring \
    php82-xml \
    php82-json \
    php82-session \
    php82-ctype \
    php82-tokenizer \
    php82-dom \
    php82-curl \
    php82-filter \
    php82-hash \
    php82-iconv \
    php82-openssl \
    php82-simplexml \
    php82-xmlreader \
    php82-xmlwriter \
    php82-zlib \
    php82-fpm # Ensure php-fpm is installed for the FPM image (this might be redundant with the base image, but harmless)

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