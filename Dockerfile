FROM php:8.2-cli-alpine

# System dependencies
RUN apk add --no-cache \
    bash \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    nodejs \
    npm \
    freetype-dev \
    libjpeg-turbo-dev

# PHP extensions (GD needed for dompdf)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pgsql pdo_pgsql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app
COPY . .

# PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets (Vite)
RUN npm ci && npm run build && rm -rf node_modules

# Storage permissions
RUN chmod -R 775 storage bootstrap/cache

CMD ["bash", "/var/www/html/start.sh"]
