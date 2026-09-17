FROM php:7.4-apache

WORKDIR /etc/apache2

RUN \
    a2enmod rewrite \
    && cat apache2.conf | sed -e '172c\\tAllowOverride All' apache2.conf > apache2.conf.bk \
    && rm apache2.conf \
    && mv apache2.conf.bk apache2.conf 

RUN docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql 
    
LABEL description="PHP + Apache-htaccess (para endpoints) + PDO"