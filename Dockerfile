# Production Dockerfile for Laravel
FROM php:8.2-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pgsql pdo_pgsql mbstring exif pcntl bcmath

# Set working directory
WORKDIR /var/www/html

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Start command - port is set at runtime
CMD ["bash", "start.sh"]