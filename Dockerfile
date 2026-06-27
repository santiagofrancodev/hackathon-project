FROM php:8.3-cli-alpine

# System dependencies
RUN apk add --no-cache \
    bash \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    nodejs \
    npm \
    freetype-dev \
    libjpeg-turbo-dev

# PHP extensions (GD needed for dompdf)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd

WORKDIR /var/www/html

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app
COPY . .

# Create minimal .env for build (overridden by Railway env vars at runtime)
RUN php -r 'file_put_contents(".env", "APP_ENV=production\nAPP_KEY=base64:" . base64_encode(random_bytes(32)) . "\nAPP_URL=https://localhost\nDB_CONNECTION=sqlite\nAPP_DEBUG=false\n");'

# PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets (Vite)
RUN npm ci && npm run build && rm -rf node_modules

# Storage permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["bash", "/var/www/html/start.sh"]
