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

FORCE_REBUILD='no';
if [ "$1" = "up" ]; then
    FORCE_REBUILD=$(php /usr/bin/php-project.phar setup-config);
fi

if [ "$FORCE_REBUILD" != "no" ] && [ "$1" = "up" ]; then
    echo -e $FORCE_REBUILD
    docker-compose up --build -d
    php /usr/bin/php-project.phar tasks;
fi

if [ "$FORCE_REBUILD" = "no" ] && [ "$1" = "up" ]; then
    docker-compose up -d
    php /usr/bin/php-project.phar tasks;
fi

if [ "$1" = "down" ]; then
    docker-compose down
fi

if [ "$1" = "bash" ]; then
    APP_NAME=$(php /usr/bin/php-project.phar app;)
    docker exec -it $APP_NAME bash
fi
