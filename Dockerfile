# Dockerfile
FROM php:8.3-apache

# Install required PHP extensions for Yii2 and MongoDB
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy Yii2 application code to the container
COPY . /var/www/html

# Set Apache environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/web

# Configure Apache
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install Composer and project dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Set proper permissions for the application
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 for the Apache server
EXPOSE 80