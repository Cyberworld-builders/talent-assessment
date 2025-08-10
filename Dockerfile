FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    wget \
    build-essential \
    python

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js 6.17.1
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

# Copy composer files (composer.lock may not exist in repo)
COPY composer.json ./

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-plugins --no-scripts --no-autoloader

# Copy application files
COPY . .

# Create storage directories and set permissions
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

RUN touch /var/www/storage/logs/laravel.log \
    && chown www-data:www-data /var/www/storage/logs/laravel.log \
    && chmod 666 /var/www/storage/logs/laravel.log

# Install Node.js dependencies and build frontend assets
RUN npm install && npm run gulp

# Generate autoloader and optimize
RUN composer dump-autoload --no-plugins --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www

# Configure Apache
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 8000
EXPOSE 8000

# Start Apache
CMD ["apache2-foreground"] 