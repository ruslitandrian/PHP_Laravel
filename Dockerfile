# Use the official PHP 8.3 FPM image as the base
FROM php:8.3-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Copy the application files
COPY . .

# Copy SQLite database
COPY database/database.sqlite /var/www/html/database/database.sqlite

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Prepare application configuration
RUN cp env.example .env \
    && php artisan key:generate --force

# Install Node.js dependencies and build frontend assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy the Nginx configuration (single container environment uses localhost connection to PHP-FPM)
COPY docker/nginx/default.single.conf /etc/nginx/sites-available/default

# Copy the Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create required directories
RUN mkdir -p /var/log/supervisor

# Expose the port
EXPOSE 80

# Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
