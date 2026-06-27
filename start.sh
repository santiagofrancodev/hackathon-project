#!/usr/bin/env bash

echo "Caching config..."
php artisan config:cache

echo "Attempting migrations (will retry on next deploy if DB not ready)..."
php artisan migrate --force || echo "Migration failed - DB may not be connected yet"

echo "Attempting seed..."
php artisan db:seed --class=DiagnosticSeeder --force || echo "Seed failed - DB may not be connected yet"

echo "Starting Laravel server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"