#!/bin/bash

HOST_DEVICE_IP=$(ifconfig wlp0s20f3 | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1')
echo $HOST_DEVICE_IP

grep '^xdebug.client_host ' ./.docker/php/xdebug.ini
sed -i "s,^xdebug.client_host =.*$,xdebug.client_host = ${HOST_DEVICE_IP}," ./.docker/php/xdebug.ini

docker-compose up --build -d