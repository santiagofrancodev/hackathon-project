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
RUN echo "APP_ENV=production" > .env && \
    echo "APP_KEY=$(php -r 'echo \"base64:\" . base64_encode(random_bytes(32));')" >> .env && \
    echo "DB_CONNECTION=sqlite" >> .env && \
    echo "APP_DEBUG=false" >> .env

# PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets (Vite)
RUN npm ci && npm run build && rm -rf node_modules

# Storage permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["bash", "/var/www/html/start.sh"]
