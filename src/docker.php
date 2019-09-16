<?php

# -------------------------------------------------------
# Docker PHP Project
# -------------------------------------------------------
# Este arquivo possui as configurações usadas para 
# subir os containers do projeto docker
#
# Para mais informações, acesse: 
# https://github.com/ricardopedias/docker-php-project

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
    
    
