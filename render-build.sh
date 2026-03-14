#!/usr/bin/env bash
set -e

echo "==> Installing PHP 8.2 and extensions..."
apt-get update -qq
apt-get install -y -qq \
    php8.2 \
    php8.2-cli \
    php8.2-mysql \
    php8.2-pdo \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-gd \
    php8.2-tokenizer \
    php8.2-fileinfo \
    unzip \
    curl

echo "==> Installing Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

echo "==> PHP version:"
php --version

echo "==> Composer version:"
composer --version

echo "==> Installing PHP dependencies (Composer)..."
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

echo "==> Installing Node dependencies..."
npm ci

echo "==> Building frontend assets..."
npm run build

echo "==> Caching Laravel config/routes/views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Build complete!"