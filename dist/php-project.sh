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
    FORCE_REBUILD=$(php /usr/bin/php-project.phar check-config-changes);
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

APP_NAME=$(php /usr/bin/php-project.phar info php-name);
MYSQL_NAME=$(php /usr/bin/php-project.phar info mysql-name);

if [ "$OPERATION" = "up" ]; then
    docker exec -it $APP_NAME bash /root/boot.sh
fi

if [ "$OPERATION" = "bash" ]; then
    USER=$(whoami) 
    docker exec -it --user $USER $APP_NAME bash
fi

if [ "$OPERATION" = "mysql" ] && [ "$MYSQL_NAME" != "none" ]; then
    MYSQL_USER=$(php /usr/bin/php-project.phar info mysql-user;)
    echo -e "\e[34mConectando no MySQL como $MYSQL_USER \033[0m";
    docker exec -it $MYSQL_NAME mysql -u $MYSQL_USER -p
fi

if [ "$OPERATION" = "mysql-root" ] && [ "$MYSQL_NAME" != "none" ]; then
    echo -e "\e[34mConectando no MySQL como root \033[0m";
    docker exec -it $MYSQL_NAME mysql -u root -p
fi
