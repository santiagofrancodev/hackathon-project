#!/usr/bin/env bash
set -e

echo "Caching config..."
php artisan config:cache

echo "Running migrations..."
until php artisan migrate --force; do
    echo "Migration failed, retrying in 2s..."
    sleep 2
done

echo "Seeding diagnostic questions..."
php artisan db:seed --class=DiagnosticSeeder --force

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=$PORT