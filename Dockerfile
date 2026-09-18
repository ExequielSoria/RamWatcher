FROM php:7.4-apache

WORKDIR /etc/apache2

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/app/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

RUN docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql

LABEL description="PHP + Apache-htaccess (para endpoints) + PDO"