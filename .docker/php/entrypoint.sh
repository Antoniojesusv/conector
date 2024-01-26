#!/bin/bash

WORKDIR="/var/www"

mkdir -p /var/run/php/
chown -R $USER:www-data /var/run
chown -R $USER:www-data $WORKDIR/.env
chown -R $USER:www-data $WORKDIR/public/temporaryImages
chown -R $USER:www-data $WORKDIR/Eurowin/fotoseurowin
chown -R $USER:www-data $WORKDIR/fotoseurowin
chown -R $USER:www-data $WORKDIR/images-log.json
chown -R $USER:www-data $WORKDIR/articles-log.json

composer install --no-plugins ; php-fpm