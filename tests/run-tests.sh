#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este script efetua as rotinas de teste para o comando 
# docker-php-project
# 1. remove uma versão do docker-php-project instalada no sistema;
# 2. compila uma nova versão do pacote;
# 3. instala a nova versão no sistema;
# 4. executa os testes de criação do projeto com o novo programa;
# 

ROOT_DIR=$(dirname "$(cd "$(dirname "$0")" && pwd)");
TESTS_DIR=$ROOT_DIR/tests;

# reinstala o pacote
./pkg-install.sh

# efetua os testes
cd $TESTS_DIR;
./test-generate-project.sh
./test-generate-php.sh
./test-generate-nginx.sh
./test-generate-mysql.sh
./test-generate-full.sh
