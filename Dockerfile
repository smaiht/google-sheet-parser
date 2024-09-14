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


# Копируем только файлы composer
COPY composer.json composer.lock ./

# Устанавливаем зависимости
RUN composer install --no-scripts --no-autoloader

# Копируем остальные файлы приложения
COPY . .

# Генерируем автозагрузчик
RUN composer dump-autoload --optimize

# Устанавливаем права доступа
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} + \
    && chmod -R 775 /var/www/html/runtime /var/www/html/web/assets



    

# Set Apache environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/web
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf


# Expose port 80 for the Apache server
EXPOSE 80