# Use an official PHP-FPM image based on Debian (Bullseye is a stable release)
FROM php:8.2-fpm-bullseye

# Install system dependencies (like nginx) and necessary libraries for PHP extensions
# Use apt-get for Debian-based images. Note the different package names for libraries!
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
    # Clean up apt cache to keep image size down
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using docker-php-ext-install
# GD configuration remains similar
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

# Install APCu (it's a PECL extension, so it's installed differently)
RUN pecl install apcu && docker-php-ext-enable apcu

# Install Composer (if your project uses Composer dependencies)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
# If your project has a composer.json, uncomment the line below to install dependencies:
# RUN composer install --no-dev --optimize-autoloader

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy your PHP application code
COPY . /var/www/html/pch
WORKDIR /var/www/html/pch

# Set proper permissions (important for web servers)
RUN chown -R www-data:www-data /var/www/html/pch
RUN find /var/www/html/pch -type d -exec chmod 755 {} +
RUN find /var/www/html/pch -type f -exec chmod 644 {} +

# Create the startup script to run both services
# This is a key change to ensure both Nginx and PHP-FPM start correctly
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80 for Nginx
EXPOSE 80

# Use the startup script as the container's entrypoint
CMD ["/usr/local/bin/start.sh"]
