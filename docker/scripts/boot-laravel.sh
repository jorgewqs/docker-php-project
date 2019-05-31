#!/bin/bash

#---------------------------------------------------------------------------------------------------
# Este programa prepara um projeto laravel automaticamente
#---------------------------------------------------------------------------------------------------

set -e

# Cores
RED='\033[0;31m';
YELLOW='\e[33m';
GREEN='\033[0;32m';
NORMAL='\033[0m';

# COMPOSER
if [ ! -f '/usr/local/bin/composer' ]; then

    echo "-----------------------------------------------------------";
    echo -e "Instalando o composer";
    echo "-----------------------------------------------------------";
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
    echo -e "${GREEN}[Ok] Instalação concluída ${NORMAL}";

fi

if [ -d '/var/www/html/app' ]; then

    echo "-----------------------------------------------------------";
    echo -e "Detectada uma aplicação laravel existente";
    echo "-----------------------------------------------------------";

fi

# BIBLIOTECAS
if [ ! -d '/var/www/html/vendor' ]; then

    echo "-----------------------------------------------------------";
    echo -e "Instalando as dependências";
    echo "-----------------------------------------------------------";

    composer install

fi

# ENVIRONMENT
if [ ! -f '/var/www/html/.env' ]; then

    echo "-----------------------------------------------------------";
    echo -e "Configurando a aplicação laravel";
    echo "-----------------------------------------------------------";

    cp .env.example .env
    echo -e "Criando o arquivo .env: ${GREEN}[Ok]${NORMAL}";

    # extrai as informações do docker-compose.yml
    YML_HOST=$(awk -F "=" '/MYSQL_HOST/ {print $2}' /var/www/html/docker-compose.yml);
    YML_PORT=$(awk -F "=" '/MYSQL_PORT/ {print $2}' /var/www/html/docker-compose.yml);
    YML_DATABASE=$(awk -F "=" '/MYSQL_DATABASE/ {print $2}' /var/www/html/docker-compose.yml);
    YML_USERNAME=$(awk -F "=" '/MYSQL_USER/ {print $2}' /var/www/html/docker-compose.yml);
    YML_PASSWORD=$(awk -F "=" '/MYSQL_PASSWORD/ {print $2}' /var/www/html/docker-compose.yml);

    # aplica no .env

    sed -i "/^DB_HOST=/s/=.*/=$YML_HOST/" .env
    echo -e "Setando DB_HOST: ${YELLOW}$YML_HOST${NORMAL}";

    sed -i "/^DB_PORT=/s/=.*/=$YML_PORT/" .env
    echo -e "Setando DB_PORT: ${YELLOW}$YML_PORT${NORMAL}";

    sed -i "/^DB_DATABASE=/s/=.*/=$YML_DATABASE/" .env
    echo -e "Setando DB_DATABASE: ${YELLOW}$YML_DATABASE${NORMAL}";

    sed -i "/^DB_USERNAME=/s/=.*/=$YML_USERNAME/" .env
    echo -e "Setando DB_USERNAME: ${YELLOW}$YML_USERNAME${NORMAL}";

    sed -i "/^DB_PASSWORD=/s/=.*/=$YML_PASSWORD/" .env
    echo -e "Setando DB_PASSWORD: ${YELLOW}$YML_PASSWORD${NORMAL}";


    echo "-----------------------------------------------------------";
    echo -e "gerando a chave criptográfica";
    echo "-----------------------------------------------------------";

    # echo Gerando a chave criptográfica
    php artisan key:generate

fi

# PERMISSOES

echo "-----------------------------------------------------------";
echo -e "Aplicando as permissões";
echo "-----------------------------------------------------------";

chown -Rf www-data:www-data .
echo -e "Setando dono dos arquivos: ${YELLOW}www-data${NORMAL}";
echo -e "Setando grupo dos arquivos: ${YELLOW}www-data${NORMAL}";

find . -type d -exec chmod 775 {} \;
echo -e "Setando permissão para diretórios: ${YELLOW}775${NORMAL}";

find . -type f -exec chmod 664 {} \;
echo -e "Setando permissão para arquivos: ${YELLOW}664${NORMAL}";

chmod -R ug+rwx storage
# chgrp -R www-data storage bootstrap/cache
echo -e "Setando permissão de escrita para storage: ${GREEN}OK${NORMAL}";

chmod -R ug+rwx bootstrap/cache
# chmod -R ug+rwx storage bootstrap/cache
echo -e "Setando permissão de escrita para bootstrap/cache: ${GREEN}OK${NORMAL}";


# BANCO DE DADOS
echo "-----------------------------------------------------------";
echo -e "Preenchendo o banco de dados";
echo "-----------------------------------------------------------";

php artisan migrate
echo -e "Executando migrations: ${GREEN}OK${NORMAL}";

php artisan db:seed
echo -e "Executando seeds: ${GREEN}OK${NORMAL}";

# if [ $mode != 'local' ]; then 

#     echo "MODO: Produção ($mode)";
#     echo 'Este comando não pode ser executado em produção!';
#     exit;

# else

#     echo "MODO: Desenvolvimento ($mode)";

# fi
