#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este programa compila um novo pacote e reinstala no sistema
# 

RED='\033[0;31m';
GREEN='\033[0;32m';
BLUE='\e[34m';
NC='\033[0m';

PATH_ROOT=$(cd "$(dirname "$0")" && pwd);
PATH_DIST="$PATH_ROOT/dist";
PHAR_FILE="$PATH_ROOT/dist/php-project.phar";

echo -e "${BLUE}→ Removendo pacote atual${NC}";
sudo dpkg -r docker-php-project;
# resolve quaisquer pacotes quebrados
sudo apt-get -qq clean;
sudo apt-get -qq autoremove;
sudo apt-get -f -qq install;
sudo dpkg --configure -a;

echo -e "${BLUE}→ Compilando novo programa${NC}";
sudo $PATH_ROOT/make-phar.php;

echo -e "${BLUE}→ Gerando novo pacote .deb${NC}";
sudo $PATH_ROOT/make-deb.sh;

cd $PATH_DIST;
LAST_PACKAGE=$(ls *.deb | tail -1);
echo -e "${BLUE}→ Instalando $LAST_PACKAGE${NC}";
sudo dpkg -i $LAST_PACKAGE;

cd $PATH_ROOT;

echo -e "${GREEN}✔  Nova versão disponibilizada!${NC}";
