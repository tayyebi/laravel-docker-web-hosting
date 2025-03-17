#!/bin/bash

# Exit on error
set -e

# Fresh breath
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Run composer
composer install

# Run database migrations
php artisan migrate --force

# Start Horizon in the background
php artisan horizon &

# Start the application
exec "$@"
