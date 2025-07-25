FROM php:8.1-apache

# Install ekstensi MySQLi dan PDO MySQL
RUN docker-php-ext-install mysqli pdo_mysql

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Salin file project ke dalam container
COPY . /var/www/html/

# Set permission
RUN chown -R www-data:www-data /var/www/html
