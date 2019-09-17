# Docker PHP Project

By [Ricardo Pereira Dias](http://www.ricardopdias.com.br) ©

![PHP Version](https://img.shields.io/badge/php-%5E7.1.3-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
![Codacy Badge](https://api.codacy.com/project/badge/Grade/7371a9ca517c4127ad32b6b242df9d8d)
![Follow](https://img.shields.io/github/followers/ricardopedias?label=Siga%20no%20GitHUB&style=social)
![Twitter](https://img.shields.io/twitter/follow/ricardopedias?label=Siga%20no%20Twitter)

Este é um pacote de software para distribuições Linux baseadas em Debian que permite a criação de projetos PHP com Docker de forma flexível e rápida.

Para usar:

1. Faça o download e instale a última versão do pacote [docker-php-project_2.2.1_all.deb](https://github.com/ricardopedias/docker-php-project/raw/master/dist/docker-php-project_2.2.1_all.deb);
2. Abra o terminal e execute o comando "php-project" em qualquer lugar para gerar projetos do Docker :)

## 1. Objetivo

O objetivo desta ferramenta é possibilitar a execução de qualquer projeto PHP sem a necessidade de instalar a infraestrutura (web server, linguagem e banco de dados) no computador do desenvolvedor. Como esta ferramenta está em evolução, usada para fins reais de trabalho, novidades poderão surgir e novas funcionalidades poderão 
ser adicionadas para facilitar ainda mais o processo de configuração.

## 2. Funcionamento

### 2.1. Comandos básicos

Para executar a infraestrutura do projeto, é necessário existir um arquivo chamado "docker.php" no diretório atual.
Para criá-lo basta executar:

```sh
$ php-project init
```

Para gerar os arquivos do Docker (docker-compose.yml, .docker-project/*) e subir so containers do projeto:

```sh
$ php-project up
```

### 2.2. O docker.php

O arquivo **docker.php** é a porta de entrada para uma fácil configuração. Quando um projeto é iniciado,
o arquivo padrão contém as seguintes configurações.

```php
php('7.3')
    ->param('---name---', 'app')
    ->extension('mysql')
    ->extension('gd')
    ->tool('composer')
    ->tool('git');

nginx('1.17')
    ->param('---name---', 'webserver')
    ->param('port', 30000)
    ->param('redir-index', true)
    ->param('redir-target', 'public/index.php')
    ->param('client-max-body-size', '108M');

mysql('5.7')
    ->param('---name---', 'database')
    ->param('port', 30002)
    ->param('dbname', 'app_database')
    ->param('user', 'dbuser')
    ->param('pass', 'secret')
    ->param('root-pass', 'secret')
    ->param('init-database', 'true')
    ->param('init-database-path', 'database');

task('test')
    ->run('echo "Tarefa executada";');
```

### 2.3. As ferramentas disponíveis

Até o presente momento, o Docker PHP Project suporta as seguintes ferramentas:

| Componente | Versão                   |
| :--------- | :----------------------- | 
| nginx	     | 1.15 e 1.16              | 
| mysql	     | 5.5, 5.6, 5.7 e 8        |
| php	     | 5.6, 7.0, 7.1, 7.2 e 7.3 |

### 2.4. Tarefas

Além de configurar e executar automaticamente os containers, é possível 
atribuir tarefas para o projeto. Todas as vezes que o projeto subir, essas tarefas serão executadas:

```php
task('permissoes')
    ->run('chmod -Rf 755 /bootstrap/cache');
    ->run('chmod -Rf 755 /storage')
    ->run('php artisan migrate');
```

