# Use an official PHP-FPM image based on Debian (Bullseye is a stable release)
FROM php:8.2-fpm-bullseye

# Install system dependencies and necessary libraries for PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    build-essential \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libicu-dev \
    git \
    zlib1g-dev \
    libxml2-dev \
    libonig-dev \
    # Clean up apt cache to keep image size down
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using docker-php-ext-install
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp && \
    docker-php-ext-install -j$(nproc) \
    gd \
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

# Install APCu (PECL extension)
RUN pecl install apcu && docker-php-ext-enable apcu

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy your PHP application code
COPY . /var/www/html/pch
WORKDIR /var/www/html/pch

# Set proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html/pch
RUN find /var/www/html/pch -type d -exec chmod 755 {} +
RUN find /var/www/html/pch -type f -exec chmod 644 {} +

# Create and copy the startup script to run both services
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80 for Nginx
EXPOSE 80

# Use the startup script as the container's entrypoint
CMD ["/usr/local/bin/start.sh"]
