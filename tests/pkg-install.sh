#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este script efetua a instalação do pacote no sistema 
# para efetuar os testes como se fosse o ambiente real
# 

TESTS=$(pwd)
DIST=$(dirname $(cd "$(dirname "$0")" && pwd))/dist;
ROOT=$(dirname $DIST);

echo "=======================================================================================";
echo "=======================================================================================";
echo "";
echo "Recompilando o pacote do programa";
echo "";
echo "=======================================================================================";
echo "=======================================================================================";
echo "Limpando o sistema e preparando...";
echo "--------------------------------------"

# remove a última versão do pacote
sudo dpkg -r docker-php-project;

# resolve quaisquer pacotes quebrados
sudo apt-get clean;
sudo apt-get autoremove;
sudo apt-get -f install;
sudo dpkg --configure -a;

# compila o novo pacote
echo "--------------------------------------"
echo "Compilando nova versão do docker-php-project";
echo "--------------------------------------"
cd $DIST;
./make-deb.sh

# instala a última versão do pacote
LAST_PACKAGE=$(ls *.deb | tail -1);
echo "--------------------------------------"
echo "Instalando $LAST_PACKAGE";
echo "--------------------------------------"
sudo dpkg -i $LAST_PACKAGE;

cd $TESTS;