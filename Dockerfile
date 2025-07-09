FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure PHP
RUN echo "session.save_handler = files" >> /usr/local/etc/php/php.ini \
    && echo "session.save_path = /tmp" >> /usr/local/etc/php/php.ini

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"] 