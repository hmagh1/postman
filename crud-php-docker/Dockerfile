FROM php:8.1-apache

# extensions
RUN docker-php-ext-install pdo pdo_mysql

# activer mod_rewrite
RUN a2enmod rewrite

# autoriser .htaccess dans /var/www/html
RUN printf '<Directory /var/www/html>\n    AllowOverride All\n</Directory>\n' \
    >> /etc/apache2/apache2.conf
