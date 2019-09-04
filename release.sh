#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este programa gera um novo release do software.
# Ele seta uma nova versão, compila um novo pacote e atualiza o repositório
# 

ROOT=$(cd "$(dirname "$0")" && pwd);

RED='\033[0;31m';
GREEN='\033[0;32m';
NC='\033[0m';

VERSION=$(cd $ROOT ; git describe --tags $(git rev-list --tags --max-count=1));
# se a tag possuir o "v" no inicio
if [ "${VERSION:0:1}" == "v"  ]; then
    # extrai o "v"
    VERSION=${VERSION:1:5}; 
fi

echo "A versão atual é ${VERSION}.";
echo -e "${GREEN}Digite o número do novo release:${NC}";
read RELEASE_NUMBER

if [[ $RELEASE_NUMBER =~ ^[0-9]*\.[0-9]*\.[0-9]*$ ]]; then 
    echo "OK o novo release será ${RELEASE_NUMBER}";
else
    echo -e "${RED}O número de release é inválido!${NC}"
    echo "Um release válido deve possuir o formato 9.9.9!"
    exit;
fi

# seta configurações adequadas
git config user.name = 'Ricardo Pereira'
git config user.email = 'contato@ricardopdias.com.br'

# submete a nova tag
git tag -a "v${RELEASE_NUMBER}" -m '';
git push https://ricardopedias@github.com/ricardopedias/docker-php-project.git --tags

# gera um novo pacote deb
cd dist;
./make-deb.sh;

# submete o novo pacote
cd $ROOT;
git add dist/docker-php-project*
git add readme.md
git commit -m "Compilada nova versão do pacote Debian";
git push https://ricardopedias@github.com/ricardopedias/docker-php-project.git
