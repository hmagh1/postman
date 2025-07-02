FROM php:8.1-apache

# 1. Installer les paquets requis par PECL memcached
RUN apt-get update && apt-get install -y \
      libmemcached-dev \
      zlib1g-dev \
      libsasl2-dev \
      libssl-dev \
      pkg-config \
    && rm -rf /var/lib/apt/lists/*

# 2. Installer et activer l’extension memcached
RUN pecl install memcached \
    && docker-php-ext-enable memcached

# 3. Installer PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# 4. Activer mod_rewrite et autoriser .htaccess
RUN a2enmod rewrite \
 && printf '<Directory /var/www/html>\n    AllowOverride All\n</Directory>\n' \
      >> /etc/apache2/apache2.conf
