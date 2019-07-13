# Docker PHP Project

By [Ricardo Pereira Dias](http://www.ricardopdias.com.br) ©

Este é um pacote de software para distribuições Linux baseadas em Debian que permite a criação de projetos PHP com Docker de forma flexível e rápida.

Para usar:

1. Faça o download e instale a última versão do pacote [docker-php-project\_X.X.X\_all.deb](https://github.com/ricardopedias/docker-php-project/raw/master/dist/docker-php-project_1.0.0_all.deb);
2. Abra o terminal e execute o comando "docker-php-project" em qualquer lugar para gerar projetos do Docker :)

# 1. Objetivo

O objetivo inicial desta ferramenta é possibilitar a execução de qualquer projeto PHP sem a necessidade de instalar a infraestrutura (web server, linguagem e banco de dados) no computador do desenvolvedor.

Até o presente momento, este pacote não provê quaisquer abordagens de segurança para que possa ser executado em produção. 
Isso não significa que não possa ser usado como ponto de partida para uma configuração mais minuciosa de forma a adequar o projeto 
para as respectivas necessidades de produção.

Como esta ferramenta está em evolução, usada para fins reais de trabalho, novidades poderão surgir e novas funcionalidades poderão 
ser adicionadas para facilitar ainda mais o processo de configuração.

# 2. Funcionamento

## Arquivo docker-project.ini
Para poder gerar um projeto do Docker, é necessário existir um arquivo chamado "docker-project.ini" no diretório atual, contendo os parâmetros do projeto desejado.

Para gerar um arquivo "docker-project.ini" padrão, basta executar o comando:

```
$ docker-php-project --create
```

## Gerando os arquivos do Docker

Após refinar as configurações do projeto no arquivo docker-project.ini", basta executar o comando "docker-php-project" para compilar os arquivos necessários do projeto.

```
$ docker-php-project
```

A ferramenta criará vários arquivos, sendo eles:

Arquivo            | Descrição
------------------ | -------------
DockerfilePHP      | O arquivo de compilação da imagem do PHP
DockerfileNGINX    | O arquivo de compilação da imagem do Nginx
DockerfileMYSQL    | O arquivo de compilação da imagem do MySQL
docker-php.ini     | O arquivo de configuração do PHP
docker-nginx.conf  | O arquivo de configuração do Nginx
docker-mysql.cnf   | O arquivo de configuração do MySQL
docker-compose.yml | O arquivo de definições para o docker-compose

Com estes arquivos gerados, basta executar normalmente o "docker-compose up" para subir os containers :)

```
$ docker-compose up
```

## Atualizando os arquivos do Docker

Caso seja necessário alterar as configurações no arquivo "docker-project.ini", basta 
executar novamente o "docker-php-project" para atualizar as informações em todos os arquivos.
A idéia é não se preocupar com os arquivos do Docker, mas somente com o "docker-project.ini".



# 3. Características

Na configuração padrão, ao rodar o **docker-compose up** na raíz do projeto, os seguintes componentes serão levantados:

- nginx:1.16
- mysql:5.7
- php:7.3-fpm

As seguintes versões são suportadas até o momento:

Componente | Versão
---------- | -------------
nginx      | 1.15 e 1.16
mysql      | 5.5, 5.6, 5.7 e 8
php        | 5.6, 7.0, 7.1, 7.2 e 7.3

O PHP está preparado para utilizar um imenso número de extensões de maneira muito fácil, controladas pelo arquivo de configuração **docker-project.ini**. Abaixo, a lista de extensões atualmente suportadas e seus status na configuração padrão. 

> Nota: As extensões com status "Nativa" não são configuráveis, pois acompanham a instalação padrão do PHP.

- [ ] acl
- [x] bcmath
- [ ] bz2         
- [ ] calendar    
- [x] ctype       **Nativa**
- [x] curl        **Nativa**
- [ ] dba          
- [ ] dba         
- [x] date        **Nativa** 
- [x] dom         **Nativa**
- [ ] enchant     
- [ ] exif        
- [ ] ereg        
- [x] fileinfo    **Nativa**
- [x] filter      **Nativa**
- [x] ftp         **Nativa**
- [x] gd          
- [ ] gettext     
- [ ] gmp         
- [x] hash        **Nativa**
- [x] iconv       **Nativa**
- [ ] imagick      
- [ ] imap        
- [x] json        **Nativa**
- [ ] ldap        
- [x] libxml      **Nativa**
- [x] mbstring    **Nativa** 
- [ ] mhash       
- [ ] mcrypt      
- [ ] mysqli      
- [x] mysqlnd     **Nativa**
- [ ] oci8        
- [ ] odbc        
- [ ] opcache      
- [x] openssl     **Nativa**
- [x] pcre        **Nativa**
- [ ] pcntl       
- [x] pdo         **Nativa**
- [ ] pdo_dblib   
- [ ] pdo_firebird 
- [x] pdo_mysql   
- [ ] pdo_oci     
- [ ] pdo_odbc    
- [ ] pdo_pgsql   
- [x] pdo_sqlite  **Nativa**
- [ ] pgsql       
- [x] phar        **Nativa**
- [x] posix     **Nativa**
- [ ] pspell      
- [x] readline    **Nativa**
- [ ] recode      
- [x] reflection  **Nativa**
- [x] session     **Nativa**
- [ ] shmop       
- [x] simplexml   **Nativa**
- [x] sodium      **Nativa**
- [x] spl         **Nativa**
- [x] sqlite3     **Nativa**
- [x] standard    **Nativa**
- [ ] sybase_ct   
- [ ] sysvmsg     
- [ ] sysvsem     
- [ ] sysvshm     
- [ ] tidy        
- [x] tokenizer   **Nativa**
- [ ] wddx        
- [x] xml         **Nativa**
- [x] xmlreader   **Nativa**
- [ ] xmlrpc      
- [x] xmlwriter   **Nativa**
- [ ] xsl         
- [ ] zip         
- [x] zlib        **Nativa**

As seguintes ferramentas também estão disponíveis para configuração.
As marcadas estão disponíveis por padrão no container da aplicação: 

- [x] composer
- [x] git     
- [x] mysql-client
- [x] nodejs      
- [ ] supervisor  
- [x] unzip       


# 4. Dicas adicionais

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

Uma vez dentro do terminal da aplicação, é possível executar normalmente as ferramentas disponíveis.
 

```
docker exec -it app bash 
root@4f4409d25008:/var/www/html# composer install

```


## Acessando o mysql

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


#### Acessando como usuário "dbuser"

O usuário "dbuser" pode acessar somente o banco de dados da aplicação, que por padrão se chama "app_database". Para acessar, basta fornecer as seguintes credenciais:

```
Dominio: database
Porta:   20300
Usuário: dbuser
Senha:   secret
```


#### Acessando como usuário "root"

O usuário "root" pode acessar todas a parte administrativa do servidor de banco de dados. Para acessar, basta especificar as seguintes informações:

```
Dominio: database
Porta:   20300
Usuário: root
Senha:   secret
```



## Código fonte da aplicação

Por padrão, a configuração não adiciona o código fonte dentro do container resultante
(após executar o *docker-compose up*), mantendo o código livre para alteração e 
visualização do resultado em tempo real no container em execução.

