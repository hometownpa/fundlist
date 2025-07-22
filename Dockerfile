# Use an official PHP-FPM image with Nginx
FROM php:8.2-fpm-alpine # You can choose a different PHP version (e.g., 8.0-fpm-alpine, 8.3-fpm-alpine)

# Install system dependencies and PHP extensions
RUN apk update && apk add --no-cache \
    nginx \
    php8-pecl-apcu \
    php8-pdo_mysql \
    php8-mysqli \
    php8-zip \
    php8-gd \
    php8-opcache \
    php8-intl \
    php8-mbstring \
    php8-xml \
    php8-json \
    php8-session \
    php8-ctype \
    php8-tokenizer \
    php8-dom \
    php8-curl \
    php8-filter \
    php8-hash \
    php8-iconv \
    php8-openssl \
    php8-simplexml \
    php8-xmlreader \
    php8-xmlwriter \
    php8-zlib \
    php8-fpm # Ensure php-fpm is installed for the FPM image

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy your PHP application code
COPY . /var/www/html/pch # Assuming your PHP files are in the root of your Git repo
WORKDIR /var/www/html/pch

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/pch
RUN find /var/www/html/pch -type d -exec chmod 755 {} +
RUN find /var/www/html/pch -type f -exec chmod 644 {} +

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD sh -c "php-fpm && nginx -g 'daemon off;'"