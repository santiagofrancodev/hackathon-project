#!/usr/bin/env bash

set -e

__DIR__="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Ensure SQLite database exists
touch "${__DIR__}/database/database.sqlite"

echo "Caching config..."
php artisan config:cache

echo "Starting Laravel server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
