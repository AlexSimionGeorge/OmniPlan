#!/bin/bash

cd /srv/app/ || exit 1

composer install

service nginx start
php-fpm -F