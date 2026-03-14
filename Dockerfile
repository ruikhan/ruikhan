# Use Node 20 as base, then add PHP on top
FROM node:20-bullseye-slim

# Install PHP 8.2 and required extensions
RUN apt-get update && apt-get install -y \
    lsb-release ca-certificates apt-transport-https software-properties-common curl git zip unzip \
    && curl -sSL https://packages.sury.org/php/README.txt | bash -x \
    && apt-get update && apt-get install -y \
        php8.2-cli \
        php8.2-mysql \
        php8.2-mbstring \
        php8.2-xml \
        php8.2-curl \
        php8.2-zip \
        php8.2-bcmath \
        php8.2-gd \
        php8.2-fileinfo \
        php8.2-tokenizer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first (better layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files
COPY package.json package-lock.json ./

# Install Node dependencies
RUN npm ci

# Copy everything else
COPY . .

# Run composer scripts now all files are present
RUN composer dump-autoload --optimize --no-interaction

# Build frontend assets (Ziggy vendor folder now exists)
RUN npm run build

# Cache Laravel
RUN php8.2 artisan config:cache \
    && php8.2 artisan route:cache \
    && php8.2 artisan view:cache

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php8.2 artisan migrate --force && php8.2 artisan storage:link --force && php8.2 -S 0.0.0.0:$PORT -t public