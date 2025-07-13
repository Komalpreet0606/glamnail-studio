# Use PHP with Apache
FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files into container
COPY . /var/www/html/

# Go to project directory and install dependencies
WORKDIR /var/www/html
RUN composer install

# Enable mod_rewrite if needed
RUN a2enmod rewrite
