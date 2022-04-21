#!/bin/bash

WORKDIR="/var/www"

chown -R $USER:www-data $WORKDIR/.env
chown -R $USER:www-data $WORKDIR/public/temporaryImages
chown -R $USER:www-data $WORKDIR/Eurowin/fotoseurowin
chown -R $USER:www-data $WORKDIR/images-log.json

composer update --no-plugins ; php-fpm