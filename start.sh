#!/usr/bin/env bash

echo "Waiting for database to be ready..."
for i in {1..30}; do
    # Try DATABASE_URL first (Render format)
    if [ -n "$DATABASE_URL" ]; then
        if php -r "new PDO('$DATABASE_URL');" 2>/dev/null; then
            echo "Database ready!"
            break
        fi
    fi
    # Or try individual DB_ vars
    if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then
        if php -r "new PDO('pgsql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; then
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