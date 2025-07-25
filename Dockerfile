FROM php:8.1-apache

# Install ekstensi MySQLi
RUN docker-php-ext-install mysqli

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Salin file project ke dalam container
COPY . /var/www/html/

# Set permission
RUN chown -R www-data:www-data /var/www/html
