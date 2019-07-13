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

TEST="Emissão de erro na tentativa de criação de projeto sem arquivo de configuração";

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

TEST="Emissão de erro para arquivo de configuração corrompido";

cat > docker-project.ini <<EOF 
[php]
php_version(~ = 7.3 # 5.6, 7.0, 7.1, 7.2, 7.3
php_apc = false
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

TEST="Arquivo de configuração sintaticamente válido";

cat > docker-project.ini <<EOF 
[php]
php_version = 7.3 # 5.6, 7.0, 7.1, 7.2, 7.3
php_apc = true
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
