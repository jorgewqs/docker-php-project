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

############################################

TEST="Emissão de erro para versão inválida do MYSQL";

cat > docker-project.ini <<EOF 
[mysql]
mysql_version = 5.0
EOF

{
    docker-php-project
} >> /dev/null

ERROR=$?;
if [ $ERROR != "0" ]; then 
    echo -e "${GREEN}[OK]${NC} ${TEST}";
else
    echo -e "${RED}[FAIL]${NC} ${TEST}";
fi

############################################

TEST="Geração do Dockerfile do MYSQL";

cat > docker-project.ini <<EOF 
[mysql]
mysql_version = 5.7
EOF

{
    docker-php-project
} >> /dev/null

ERROR=$?;
if [ $ERROR == "0" ]; then 
    echo -e "${GREEN}[OK]${NC} ${TEST}";
else
    echo -e "${RED}[FAIL]${NC} ${TEST}";
fi

# # remove o diretório com o repositório de testes
# if [ -d "$PROJECT_DIR" ]; then
#     rm -Rf $PROJECT_DIR;
# fi