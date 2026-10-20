# check=skip=LegacyKeyValueFormat
FROM php:7.4-apache

WORKDIR /etc/apache2

RUN apt-get update --fix-missing \
    && apt-get install -y --no-install-recommends cron \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/app/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

RUN docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql

COPY docker/ramwatcher-cron /etc/cron.d/ramwatcher-updater

RUN chmod 0644 /etc/cron.d/ramwatcher-updater \
    && chmod +x /usr/sbin/cron

LABEL description="PHP + Apache + PDO + cron para actualizar precios"