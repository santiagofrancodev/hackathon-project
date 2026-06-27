#!/usr/bin/env bash

echo "Caching config..."
php artisan config:cache

echo "Waiting for database to be ready..."
for i in {1..30}; do
    # Check if DB env vars are set (Render provides them when DB is connected)
    if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then
        if php -r "new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; then
            echo "Database ready!"
            break
        fi
    fi
    echo "Waiting for database... ($i)"
    sleep 2
done

echo "Running migrations..."
php artisan migrate --force

echo "Seeding diagnostic questions..."
php artisan db:seed --class=DiagnosticSeeder --force

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=$PORT