# --- Build des assets (si Vite est utilisé) ---
FROM node:20-alpine AS nodebuild
WORKDIR /app
COPY package*.json ./
RUN [ -f package.json ] && npm ci || true
COPY . .
RUN [ -f package.json ] && npm run build || true

# --- Runtime PHP (serveur) ---
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    curl \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier le code source (sauf node_modules et vendor)
COPY . .

# Copier les assets compilés depuis le build Node (si Vite)
COPY --from=nodebuild /app/public/build ./public/build

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
