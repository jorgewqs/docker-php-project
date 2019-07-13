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

DIST=$(cd "$(dirname "$0")" && pwd);
SOURCE=$(dirname $(cd "$(dirname "$0")" && pwd));
ROOT=$(dirname $DIST);

VERSION=$(cd $ROOT ; git describe --tags $(git rev-list --tags --max-count=1));

# se a tag possuir o "v" no inicio
if [ "${VERSION:0:1}" == "v"  ]; then
    # extrai o "v"
    VERSION=${VERSION:1:5}; 
fi

# gera a estrutura do pacote
mkdir -p docker-php-project/DEBIAN
mkdir -p docker-php-project/usr/share/docker-php-project
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

echo "------------------------------------------";
# copia o código fonte do docker-php-project
echo "Copiando $SOURCE para $DIST/docker-php-project/usr/share";
rm -Rf /tmp/docker-php-project
cp -r $SOURCE /tmp
cp -r /tmp/docker-php-project $DIST/docker-php-project/usr/share
echo "------------------------------------------";
echo "Limpando $DIST/docker-php-project/usr/share/docker-php-project";
rm -Rf $DIST/docker-php-project/usr/share/docker-php-project/.git
rm -Rf $DIST/docker-php-project/usr/share/docker-php-project/dist
rm -Rf $DIST/docker-php-project/usr/share/docker-php-project/tests
rm -Rf $DIST/docker-php-project/usr/share/docker-php-project/docker-compose.yml
rm -Rf $DIST/docker-php-project/usr/share/docker-php-project/public
echo "------------------------------------------";
echo "Gerando link simbólico 'docker-project' em $DIST/docker-php-project/usr/share/docker-php-project/usr/bin/";
chmod a+x $DIST/docker-php-project/usr/share/docker-php-project/docker-php-project.php
ln -s /usr/share/docker-php-project/docker-php-project.php $DIST/docker-php-project/usr/bin/docker-php-project
echo "------------------------------------------";
# gera o pacote deb
dpkg-deb -b $DIST/docker-php-project/ $DIST

# remove os arquivo gerados na instalação
rm -Rf docker-php-project