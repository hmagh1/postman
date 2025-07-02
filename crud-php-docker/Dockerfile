FROM php:8.1-apache

# 1) Installer les paquets requis pour Memcached et Composer
RUN apt-get update && apt-get install -y \
      libmemcached-dev \
      zlib1g-dev \
      libsasl2-dev \
      libssl-dev \
      pkg-config \
      git \
      unzip \
    && rm -rf /var/lib/apt/lists/*

# 2) Installer et activer l’extension Memcached
RUN pecl install memcached \
    && docker-php-ext-enable memcached

# 3) Installer les extensions PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# 4) Activer mod_rewrite et autoriser .htaccess
RUN a2enmod rewrite \
 && printf '<Directory /var/www/html>\n    AllowOverride All\n</Directory>\n' \
      >> /etc/apache2/apache2.conf

# 5) Installer Composer depuis l’image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6) Définir le répertoire de travail
WORKDIR /var/www/html

# 7) Copier composer.json et installer les dépendances
COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 8) Copier le reste de l’application
COPY . .

# 9) Exposer le port Apache
EXPOSE 80
