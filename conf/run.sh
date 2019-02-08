#!/bin/bash

chmod -Rf 777 /app/storage

php-fpm -F --pid /opt/bitnami/php/tmp/php-fpm.pid -y /opt/bitnami/php/etc/php-fpm.conf &
nginx -c /etc/nginx/nginx.conf -g "daemon off;"
