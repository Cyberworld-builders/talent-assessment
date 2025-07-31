FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    python \
    build-essential

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Set proper permissions
RUN chown -R www-data:www-data /var/www

# Create storage directories and set permissions
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

# Create laravel.log file
RUN touch /var/www/storage/logs/laravel.log \
    && chown www-data:www-data /var/www/storage/logs/laravel.log \
    && chmod 666 /var/www/storage/logs/laravel.log

# Allow Composer plugin
RUN composer config --no-plugins allow-plugins.kylekatarnls/update-helper true

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Clear compiled classes
RUN php artisan clear-compiled

# Commented out due to encoding errors
# RUN php artisan optimize

# Expose port 8000
EXPOSE 8000

# Start Laravel development server
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=8000 || echo 'Failed to start Laravel' && sleep 10"] 