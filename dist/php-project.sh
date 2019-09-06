#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este script é uma ponte para invocação do arquivo phar
# 

php /usr/bin/php-project.phar $@

if [ "$1" = "up" ]; then
    docker-compose $@
fi

if [ "$1" = "down" ]; then
    docker-compose $@
fi