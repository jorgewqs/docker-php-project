#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este programa gera um pacote debian para instalação em distribuições Linux.
# O pacote será versionado de acordo com a última tag comitada no repositório.
#
# Mais informações sobre a criação de pacotes Debian:
# https://debian-handbook.info/browse/pt-BR/stable/sect.building-first-package.html#id-1.18.5.2
# 

RED='\033[0;31m';
GREEN='\033[0;32m';
BLUE='\e[34m';
NC='\033[0m';

PATH_ROOT=$(cd "$(dirname "$0")" && pwd);
PATH_DIST="$PATH_ROOT/dist";
PHAR_FILE="$PATH_ROOT/dist/php-project.phar";
VERSION_FILE="$PATH_ROOT/src/version.txt";

# se a tag possuir o "v" no inicio
VERSION=$(git describe --tags $(git rev-list --tags --max-count=1));
if [ "${VERSION:0:1}" == "v"  ]; then
    # extrai o "v"
    VERSION=${VERSION:1:5}; 
fi

echo "$VERSION" > "$VERSION_FILE";
 
cd $PATH_DIST;

# gera a estrutura do pacote
mkdir -p docker-php-project/DEBIAN
mkdir -p docker-php-project/usr/bin

# cria o arquivo de controle
touch docker-php-project/DEBIAN/control
cat > docker-php-project/DEBIAN/control <<EOF 
Package: docker-php-project
Priority: optional
Version: $VERSION
Architecture: all
Maintainer: Ricardo Pereira Dias <contato@ricardopdias.com.br>
Depends: php-cli
Description: Ferramenta para gerar projetos Docker para o PHP
EOF

sudo cp $PATH_DIST/php-project.phar /usr/bin/php-project.phar;
sudo cp $PATH_DIST/php-project.sh /usr/bin/php-project;
sudo chmod a+x /usr/bin/php-project;

# gera o pacote deb
dpkg-deb -b $PATH_DIST/docker-php-project/ $PATH_DIST

# remove os arquivo gerados na instalação
rm -Rf $PATH_DIST/docker-php-project

# atualiza o arquivo readme.md
echo -e "${BLUE}→ Atualizando readme.md${NC}";
cd $PATH_ROOT;
sed -i -- "s/\[docker-php-project_.*_all.deb\]/[docker-php-project_${VERSION}_all.deb]/g" readme.md
sed -i -- "s/dist\/docker-php-project_.*_all.deb/dist\/docker-php-project_${VERSION}_all.deb/g" readme.md
