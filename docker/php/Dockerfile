FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    nodejs \
    npm

# PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

# --no-scripts: post-autoload-dump hook'u (artisan package:discover) .env olmadan patlar
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy remaining project files
COPY . .

# Generate bootstrap/cache/packages.php with a temporary env
RUN printf "APP_ENV=production\nAPP_KEY=base64:%s\n" \
        "$(php -r 'echo base64_encode(random_bytes(32));')" > .env \
    && php artisan package:discover --ansi \
    && rm .env

# Install JS dependencies and build assets
RUN npm ci && npm run build && rm -rf node_modules

# Fix storage permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER www-data
