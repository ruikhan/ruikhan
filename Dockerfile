FROM node:20-bullseye-slim

# Install PHP 8.2 + extensions
RUN apt-get update && apt-get install -y \
    lsb-release ca-certificates apt-transport-https \
    software-properties-common curl git zip unzip \
    && curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg \
       https://packages.sury.org/php/apt.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" \
       > /etc/apt/sources.list.d/php.list \
    && apt-get update && apt-get install -y \
        php8.2-cli \
        php8.2-mysql \
        php8.2-mbstring \
        php8.2-xml \
        php8.2-curl \
        php8.2-zip \
        php8.2-bcmath \
        php8.2-gd \
        php8.2-intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP deps first (Ziggy needs to exist before npm build)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install Node deps
COPY package.json package-lock.json ./
RUN npm ci

# Copy all source files
COPY . .

# Run composer post-install scripts
RUN composer dump-autoload --optimize --no-interaction

# Build Vite assets (Ziggy vendor now exists)
RUN npm run build

# ⚠️  DO NOT run config:cache/route:cache here — it would bake .env values
#     into the image and ignore Render's environment variables at runtime.
#     These are run at startup instead (see CMD below).

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# At startup: cache uses LIVE env vars from Render, then migrate and serve
CMD php8.2 artisan config:cache \
    && php8.2 artisan route:cache \
    && php8.2 artisan view:cache \
    && php8.2 artisan migrate --force \
    && php8.2 artisan storage:link --force \
    && php8.2 -S 0.0.0.0:$PORT -t public