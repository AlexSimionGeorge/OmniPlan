FROM php:8.3-fpm

WORKDIR /srv/app/

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y git zip libicu-dev nginx libpq-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo opcache pdo_pgsql

RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer

COPY ./docker/docker-entrypoint.sh /docker-entrypoint.sh
COPY ./docker/default.conf /etc/nginx/sites-available/default

RUN chmod +x /docker-entrypoint.sh

RUN usermod --uid 1000 www-data \
    && groupmod --gid 1000 www-data

CMD ["/bin/bash", "-c", "/docker-entrypoint.sh"]
