<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este script gera o arquivo docker-mysql.cnf para o mysql.
 * É invocado pelo arquivo docker-mysql-project.php e 
 * fornece a variável $settings, contendo as configurações
 * setadas para a compilação
 */

$config = $settings['mysql'];

//$snippetPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'mysql']);
$configFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-mysql.cnf']);

cli_step('success', "Gerando arquivo de configuração para o MySQL {$config['mysql-version']}");

append_contents('mysql-config', "[mysqld]");
append_contents('mysql-config', "general_log = 1");
append_contents('mysql-config', "general_log_file = /var/lib/mysql/general.log");
append_contents('mysql-config', "pid-file = /var/run/mysqld/mysqld.pid");
append_contents('mysql-config', "socket = /var/run/mysqld/mysqld.sock");
append_contents('mysql-config', "datadir = /var/lib/mysql");
append_contents('mysql-config', "secure-file-priv= NULL");

append_contents('mysql-config', "# Disabling symbolic-links is recommended to prevent assorted security risks");
append_contents('mysql-config', "symbolic-links=0");

append_contents('mysql-config', "# Custom config should go here");
append_contents('mysql-config', "!includedir /etc/mysql/conf.d/");

file_put_contents($configFile, get_contents('mysql-config'));
