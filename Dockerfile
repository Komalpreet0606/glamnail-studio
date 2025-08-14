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

# Set the Apache DocumentRoot to serve from project root
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Update Apache vhost to use that docroot and allow .htaccess
RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
 && sed -ri "s!</VirtualHost>!\n\t<Directory ${APACHE_DOCUMENT_ROOT}>\n\t\tOptions Indexes FollowSymLinks\n\t\tAllowOverride All\n\t\tRequire all granted\n\t</Directory>\n</VirtualHost>!g" \
 /etc/apache2/sites-available/000-default.conf

# ensure images dir exists and is writable
RUN mkdir -p /var/www/html/images \
 && chown -R www-data:www-data /var/www/html/images \
 && chmod -R 775 /var/www/html/images
