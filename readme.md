# Docker PHP Project

By [Ricardo Pereira Dias](http://www.ricardopdias.com.br) ©

Este é um template flexível para criação de projetos PHP com Docker, configurado inicialmente para utilizar as imagens oficiais do PHP, MySQL e Nginx.

Usando este modelo, é possível tornar qualquer projeto PHP em um ambiente containerizado do Docker, abstraindo a necessidade de 
instalar o servidor web, a linguagem e o banco de dados diretamente 
no host.

Basta ter o Docker instalado e executar o **docker-compose up** dentro do diretório do projeto :)

# Objetivo

O objetivo inicial deste template é possibilitar a execução de qualquer projeto PHP de forma rápida e fácil, sem a necessidade de instalar a infraestrutura (web server, linguagem e banco de dados) no computador do desenvolvedor.

Até o presente momento, este pacote não provê quaisquer abordagens de segurança para que possa ser executado em produção. Isso não significa que o template não possa ser usado como ponto de partida 
para uma configuração mais minuciosa de forma a adequar o projeto 
para as respectivas necessidades de produção.

Como este é um repositório em evolução, usado para fins reais de trabalho, novidades poderão surgir e novas funcionalidades poderão ser adicionadas para facilitar ainda mais o processo de configuração.



# Estrutura do template

Dentro do template existem três áreas importantes:

Diretório              | Descrição
---------------------- | -------------
**docker**             | contém as configurações e ferramentas
**public**             | por padrão, é o diretório visivel na web (localhost:20100)
**docker-compose.yml** | configurações padrões para o docker-compose


Na configuração padrão, ao rodar o **docker-compose up** na raíz do template, os seguintes componentes serão levantados:

- nginx:1.16
- mysql:8.0
- php:7.3-fpm

As seguintes versões são suportadas pelo template:

Componente | Versão
-------| -------------
nginx  | 1.15 e 1.16
mysql  | 5.5, 5.6, 5.7 e 8
php    | 5.6, 7.0, 7.1, 7.2 e 7.3

O PHP está preparado para utilizar um imenso número de extensões de 
maneira muito fácil, controladas por um arquivo de configuração chamado **image-config.ini**. 
Abaixo, a lista de extensões atualmente suportadas e seu status na configuração padrão. As extensões com status "Nativa" não são configuráveis, pois acompanham a instalação padrão do PHP e com status "Instalável" são opcionais.

Status | Extensão    | Tipo
-------| ----------- | -------
x      | acl         | Instalável
OK     | bcmath      | Instalável
x      | bz2         | Instalável
x      | calendar    | Instalável
OK     | ctype       | Nativa
OK     | curl        | Nativa
x      | dba         | Instalável 
x      | dba         | Instalável
OK     | date        | Nativa 
OK     | dom         | Nativa
x      | enchant     | Instalável
x      | exif        | Instalável
x      | ereg        | Instalável
OK     | fileinfo    | Nativa
OK     | filter      | Nativa
OK     | ftp         | Nativa
OK     | gd          | Instalável
x      | gettext     | Instalável
x      | gmp         | Instalável
OK     | hash        | Nativa
OK     | iconv       | Nativa
x      | imagick     | Instalável 
x      | imap        | Instalável
OK     | json        | Nativa
x      | ldap        | Instalável
OK     | libxml      | Nativa
OK     | mbstring    | Nativa 
x      | mhash       | Instalável
x      | mcrypt      | Instalável
x      | mysqli      | Instalável
OK     | mysqlnd     | Nativa
x      | oci8        | Instalável
x      | odbc        | Instalável
x      | opcache     | Instalável 
OK     | openssl     | Nativa
OK     | pcre        | Nativa
x      | pcntl       | Instalável
OK     | pdo         | Nativa
x      | pdo_dblib   | Instalável
x      | pdo_firebird | Instalável
OK     | pdo_mysql   | Instalável
x      | pdo_oci     | Instalável
x      | pdo_odbc    | Instalável
x      | pdo_pgsql   | Instalável
OK     | pdo_sqlite  | Nativa
x      | pgsql       | Instalável
OK     | phar        | Nativa
OK     | posix       | Nativa
x      | pspell      | Instalável
OK     | readline    | Nativa
x      | recode      | Instalável
OK     | reflection  | Nativa
OK     | session     | Nativa
x      | shmop       | Instalável
OK     | simplexml   | Nativa
OK     | sodium      | Nativa
OK     | spl         | Nativa
OK     | sqlite3     | Nativa
OK     | standard    | Nativa
x      | sybase_ct   | Instalável
x      | sysvmsg     | Instalável
x      | sysvsem     | Instalável
x      | sysvshm     | Instalável
x      | tidy        | Instalável
OK     | tokenizer   | Nativa
x      | wddx        | Instalável
OK     | xml         | Nativa
OK     | xmlreader   | Nativa
x      | xmlrpc      | Instalável
OK     | xmlwriter   | Nativa
x      | xsl         | Instalável
x      | zip         | Instalável
OK     | zlib        | Nativa


As seguintes ferramentas também estão disponíveis para configuração: 

Status | Ferramenta   | Tipo
-------| -------------|
OK     | composer     | Opcional
x      | git          | Opcional
OK     | mysql-client | Opcional
OK     | nodejs       | Opcional
x      | supervisor   | Opcional


# Refinando as configurações

## MySQL e Nginx

Para mudar as versões do MySQL e Nginx, basta acessar seus respectivos 
Dockerfiles e descomentar a versão desejada.

Componente     | Arquivo
---------------| -------------
**nginx**      | docker/nginx/Dockerfile
**mysql**      | docker/mysql/Dockerfile

## PHP

### Cenário

O PHP, além de possuir várias versões distintas (5.6, 7.0, 7.1, 7.2, 7.3, etc), também possui uma 
gama de extensões para todo o tipo de necessidades. Por causa desta natureza, 
a configuração do **Dockerfile** é bem complexa e exige um conhecimento 
prévio sobre a relação de cada extensão com a versão do PHP desejada.

### Metodologia do template

Para facilitar a configuração das extensões do PHP e remover esta responsabilidade de configuração do desenvolvedor, existe um script criado para automatizar o processo. Para usá-lo existem duas ações a ser feitas: 

#### 1. Parametrizar

A primeira ação é editar o arquivo de configuração **docker/php/image-config.ini**, onde as extensões desejadas podem ser ativadas/desativadas de forma organizada, além de poder especificar a versão do PHP desejada e as ferramentas que acompanharão a imagem do Docker.

#### 2. Gerar o Dockerfile

Após a configuração desejada, é preciso executar o script **build-dockerfile.php** para gerar um novo Dockerfile:

```
$ cd docker/php
$ php build-dockerfile.php -c image-config.ini -o Dockerfile
```

Após a execução, o arquivo **docker/php/Dockerfile** do PHP será atualizado de acordo com as necessidades específicas do desenvolvedor e poderá ser executado normalmente com **docker-compose up**.

```
$ cd docker/php
$ docker-compose up
```

# Dicas adicionais

## Interagindo com a aplicação

Por padrão, a aplicação é executada no container chamado "app" (vide arquivo docker-compose.yml).

Para executar operações de terminal do container da aplicação e usufruir das ferramentas que a acompanham (conforme a configuração do PHP), existem duas maneiras:

### 1. Acessar como usuario "www-data"

O usuário padrão da aplicação é o "www-data", o mesmo usuário que executa o web server no sistema. Caso este usuário esteja sendo usado, as operações que geram arquivos irão setá-los automaticamente para o seu respectivo grupo e usuário, ou seja, "www-data".

Para acessar o container usando este usuário (com o docker-compose em execução), use o seguinte comando:

```
docker exec -it --user www-data app bash
www-data@4f4409d25008:~/html$
```

### 2. Acessar como "root"

Acessar o terminal da aplicação como "root" vai causar o mesmo efeito no método anterior, ou seja, todos os arquivos gerados durante as atividades pertencerão ao grupo e usuário "root". Isso não é bom, pois dependendo das permissões o webserver poderá perder o acesso para leitura e escrita nestes arquivos. Sendo assim, é importante lembrar se setar as permissões corretas sempre que acessar o container como "root":


```
docker exec -it app bash 
root@4f4409d25008:/var/www/html#
```



## Executando o composer

Uma vez dentro do terminal da aplicação, é possível executar normalmente as ferramentas disponíveis. Em especial, para executar o composer, existe uma opção adicional: 
 

```
docker exec -it app bash 
root@4f4409d25008:/var/www/html# composer install --prefer-dist

```


> **Nota sobre a opção --prefer-dist**: Esta opção é usada para que o Composer não tente pedir informações adicionais sobre os repositórios ao Git. Isso porque não temos o Git instalado por padrão no container. Por motivos de otimização e evitar redundância, preferiu-se executar os comandos do Git diretamente do host (computador do desenvolvedor) ao invés de instalar esta imensa ferramenta no container.

## Acessando o banco de dados


### Pelo mysql-client da aplicação

Por padrão, a aplicação vem com o cliente do mysql instalado, com a finalidade de facilitar operações específicas com banco de dados sem a necessidade de instalar no computador do desenvolvedor.

Para executar, basta observar o exemplo:

```
docker exec -it app bash 
root@4f4409d25008:/var/www/html# mysql -h database -u root -p app_database < meus_dados.sql
```

### Por um gerenciador instalado no computador

Quando o container está em execução, é possível acessá-lo normalmente, como se o software estivesse instalado diretamente no computador. Essa é a mágica da conteinerização.

Para acessar o servidor de banco de dados usando uma ferramenta de gerenciamento instalada no computador, existem duas formas:


#### 1. Acessando como usuário "dbuser"

O usuário "dbuser" pode acessar somente o banco de dados da aplicação, que por padrão se chama "app_database". Para acessar, basta fornecer as seguintes credenciais:

```
Dominio: database
Porta:   20300
Usuário: dbuser
Senha:   secret
```


#### 1. Acessando como usuário "root"

O usuário "root" pode acessar todas a parte administrativa do servidor de banco de dados. Para acessar, basta especificar as seguintes informações:

```
Dominio: database
Porta:   20300
Usuário: root
Senha:   secret
```



## Código fonte da aplicação

Por padrão, a configuração não adiciona o código fonte dentro do container resultante
(após executar o *docker-compose up*), mantendo o código lívre para alteração e 
visualização do resultado em tempo real no container em execução.

Para embutir o código fonte dentro do container, será necessário personalizar adequadamente os arquivos **docker-compose.yml** e **docker/php/Dockerfile**, incluindo os parâmetros necessários para a cópia do código fonte para a imagem e a exposição dos diretórios necessários (para que a aplicação possa gravar os arquivos direto no disco do host).

## Múltiplos projetos

Geralmente, intenção é usar o template para vários projetos PHP, e para isso será necessário fazer algumas adaptações no arquivo **docker-compose.yml**, de forma que cada projeto seja distinto.

### Nome do container

Personalize os nomes dos containers para definirem seu projeto. Por exemplo:

```
container_name: database
```

para 

```
container_name: database_webflix    # projeto 1
container_name: database_financeiro # projeto 2
container_name: database_games      # projeto 3 
```

## Portas

O mesmo tratamento deverá ser considerado para a configuração das  portas, que precisarão ser diferentes para cada projeto. Por exemplo:

```
- "3306:3306"
```

para 

```
- "4001:3306" # projeto 1
- "4101:3306" # projeto 2
- "4201:3306" # projeto 3
```
