# Use official PHP image with Apache
FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Install PDO and PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql


# Enable Apache mod_rewrite (optional but good)
RUN a2enmod rewrite

# Copy project files into the container
COPY . /var/www/html/

# Set permissions (optional)
RUN chown -R www-data:www-data /var/www/html
