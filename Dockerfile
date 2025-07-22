# Use an official PHP-FPM image based on Debian (Bullseye is a stable release)
FROM php:8.2-fpm-bullseye

# Install system dependencies (like nginx) and necessary libraries for PHP extensions
# Use apt-get for Debian-based images. Note the different package names for libraries!
RUN apt-get update && apt-get install -y \
    nginx \
    build-essential \      # Provides compilers (gcc, g++, make) - often included or handled well on Debian
    libzip-dev \           # For 'zip' extension
    libpng-dev \           # For 'gd'
    libjpeg-dev \          # For 'gd' (Debian uses libjpeg-dev, not libjpeg-turbo-dev)
    libwebp-dev \          # For 'gd'
    libfreetype6-dev \     # For 'gd' (Debian uses libfreetype6-dev, not freetype-dev)
    libicu-dev \           # For 'intl'
    git \                  # If needed for composer or other operations
    zlib1g-dev \           # For 'zlib' and other extensions (Debian uses zlib1g-dev, not zlib-dev)
    libxml2-dev \          # Often needed for 'xml', 'dom', 'simplexml', 'xmlreader', 'xmlwriter'
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

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM in the foreground
CMD ["/bin/sh", "-c", "php-fpm -F && nginx -g 'daemon off;'"]