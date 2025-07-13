# Use official PHP 8.2 + Apache image
FROM php:8.2-apache

# Install system packages required for composer and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql mysqli

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory and copy project files
WORKDIR /var/www/html
COPY . .

# Install dependencies via Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Enable Apache mod_rewrite
RUN a2enmod rewrite
