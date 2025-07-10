FROM php:8.1-apache

# Install ekstensi mysqli
RUN docker-php-ext-install mysqli

# Aktifkan mod_rewrite (kalau nanti pakai routing)
RUN a2enmod rewrite

# Copy file PHP kamu dari folder `src` ke web root Apache
COPY src/ /var/www/html/

# Set permission yang aman
RUN chown -R www-data:www-data /var/www/html
