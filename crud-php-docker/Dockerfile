FROM php:8.1-apache

# Installer les libs pour memcached + PECL + PHP extensions
RUN apt-get update && apt-get install -y \
      libmemcached-dev \
      zlib1g-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite et autoriser .htaccess
RUN a2enmod rewrite \
 && printf '<Directory /var/www/html>\n    AllowOverride All\n</Directory>\n' \
      >> /etc/apache2/apache2.conf
