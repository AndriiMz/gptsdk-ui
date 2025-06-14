#!/usr/bin/env bash
set -o errexit

# Install backend deps
composer install --no-dev --optimize-autoloader

# Install and build frontend
npm install
npm run build

# Laravel setup
php artisan migrate --force
php artisan config:cache
php artisan route:cache
