# Development stage - includes build tools for hot reloading
FROM php:7.4-apache

# Install system dependencies including build tools
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    wget \
    build-essential \
    python \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 6.17.1 (as root)
RUN wget https://nodejs.org/dist/v6.17.1/node-v6.17.1-linux-x64.tar.xz \
    && tar -xf node-v6.17.1-linux-x64.tar.xz \
    && mv node-v6.17.1-linux-x64 /opt/nodejs \
    && ln -sf /opt/nodejs/bin/node /usr/local/bin/node \
    && ln -sf /opt/nodejs/bin/npm /usr/local/bin/npm \
    && rm node-v6.17.1-linux-x64.tar.xz

# Set working directory
WORKDIR /var/www

# Disable Composer plugins when running as root in CI/containers
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_PLUGINS=1

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (including dev dependencies for development)
RUN composer install --no-plugins --no-scripts --no-autoloader

# Copy application files
COPY . .

# Create storage directories and set permissions
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chmod -R 775 /var/www/storage

RUN touch /var/www/storage/logs/laravel.log \
    && chmod 666 /var/www/storage/logs/laravel.log

# Install Node.js dependencies
RUN npm install

# Build frontend assets with gulp
RUN npm run gulp

# Generate autoloader
RUN composer dump-autoload --no-plugins --optimize

# Expose port 8000 for artisan serve
EXPOSE 8000

# Start with artisan serve for hot reloading
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]