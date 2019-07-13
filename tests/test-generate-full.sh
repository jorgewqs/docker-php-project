#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este script executa os testes de comando
#

ROOT_DIR=$(dirname "$(cd "$(dirname "$0")" && pwd)");
TESTS_DIR=$ROOT_DIR/tests;
PROJECT_DIR="${TESTS_DIR}/project";

RED='\033[0;31m';
GREEN='\033[0;32m';
NC='\033[0m';

# criação do diretório do projeto
mkdir -p $PROJECT_DIR 
cd $PROJECT_DIR;

TEST="Geração completa dos padrões";

cat ../../docker-project.ini > ./docker-project.ini;

docker-php-project

ERROR=$?;
if [ $ERROR == "0" ]; then 
    echo -e "${GREEN}[OK]${NC} ${TEST}";
else
    echo -e "${RED}[FAIL]${NC} ${TEST}";
fi
