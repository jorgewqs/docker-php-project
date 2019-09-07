#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este programa gera um novo release do software.
# Ele seta uma nova versão, compila um novo pacote e atualiza o repositório
# 

RED='\033[0;31m';
BLUE='\e[34m';
GREEN='\033[0;32m';
NC='\033[0m';

PATH_ROOT=$(cd "$(dirname "$0")" && pwd);
VERSION_FILE="$PATH_ROOT/src/version.txt";

# se a tag possuir o "v" no inicio
VERSION=$(cd $PATH_ROOT ; git describe --tags $(git rev-list --tags --max-count=1));
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
git config user.name 'Ricardo Pereira'
git config user.email 'contato@ricardopdias.com.br'

USERNAME=$(git config user.name);
EMAIL=$(git config user.email);
echo -e "Comitando como ${USERNAME} <${EMAIL}>";

# submete a nova tag
git tag -a "v${RELEASE_NUMBER}" -m '';
git push https://ricardopedias@github.com/ricardopedias/docker-php-project.git --tags

# atualiza o arquivo de versão
echo "$RELEASE_NUMBER" > "$VERSION_FILE";

echo -e "${BLUE}→ Compilando novo programa${NC}";
sudo $PATH_ROOT/make-phar.php;

# gera um novo pacote deb
./make-deb.sh;

cd $PATH_ROOT;

# submete o novo pacote
git add dist/*
git add src/version.txt
git add readme.md
git commit -m "Compilada nova versão do pacote Debian";
git push https://ricardopedias@github.com/ricardopedias/docker-php-project.git

echo -e "${GREEN}Novo release lançado com sucesso!${NC}";
