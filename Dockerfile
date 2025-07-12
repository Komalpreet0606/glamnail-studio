# Use an official PHP image with Apache
FROM php:8.2-apache

# Copy your code into the container
COPY . /var/www/html/

# Enable Apache rewrite module (if needed)
RUN a2enmod rewrite

# Set permissions (optional if needed)
RUN chown -R www-data:www-data /var/www/html
