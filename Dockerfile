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

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files and install dependencies
COPY . /var/www/html
RUN composer install

# Set Apache environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/web
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf



# Expose port 80 for the Apache server
EXPOSE 80