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

OPERATION=$1

if [ "$OPERATION" = "down" ] || [ "$OPERATION" = "reload" ]; then
    docker-compose down
fi

if [ "$OPERATION" = "reload" ]; then
    OPERATION='up'
fi

FORCE_REBUILD='no';
if [ "$OPERATION" = "up" ]; then
    FORCE_REBUILD=$(php /usr/bin/php-project.phar setup-config);
fi

if [ "$FORCE_REBUILD" != "no" ] && [ "$OPERATION" = "up" ]; then
    echo -e $FORCE_REBUILD
    docker-compose up --build -d
    php /usr/bin/php-project.phar tasks;
fi

if [ "$FORCE_REBUILD" = "no" ] && [ "$OPERATION" = "up" ]; then
    docker-compose up -d
    php /usr/bin/php-project.phar tasks;
fi

APP_NAME=$(php /usr/bin/php-project.phar app;)

if [ "$OPERATION" = "up" ]; then
    docker exec -it $APP_NAME bash /root/boot.sh
fi

if [ "$OPERATION" = "bash" ]; then
    USER=$(whoami) 
    docker exec -it --user $USER $APP_NAME bash
fi
