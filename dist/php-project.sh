#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este script é uma ponte para invocação do arquivo phar
# 

php /usr/bin/php-project.phar $@;

if [ "$?" = "1" ]; then
    exit 1;
fi

if [ "$1" = "up" ]; then
    docker-compose up -d
    php /usr/bin/php-project.phar tasks;
fi

if [ "$1" = "down" ]; then
    docker-compose down
fi
