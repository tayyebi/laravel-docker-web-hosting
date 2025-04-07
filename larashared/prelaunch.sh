#!/bin/bash

# Exit on error
set -e

# Run composer
composer install

# Run database migrations
php artisan migrate --force

# Fresh breath
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Start Horizon in the background
php artisan horizon &

# Start the application
exec "$@"
