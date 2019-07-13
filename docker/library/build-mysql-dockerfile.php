<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este arquivo gera o arquivo Dockerfile para o MYSQL.
 * É invocado pelo arquivo docker-php-project.php e 
 * fornece a variável $settings, contendo as configurações
 * setadas para a compilação
 */

$config = $settings['mysql'];

// $snippetPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'mysql']);
$dockerFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'DockerfileMYSQL']);

// VERSÃO
$version = (int) preg_replace('#[^0-9]#', '', $config['mysql-version']);
if (in_array($version, [55,56,57,80] ) == false) {
    cli_message('error', "Versão não suportada do mysql: (\"{$config['mysql-version']}\")");
    cli_message('default', "Use uma dessas: 5.5, 5.6, 5.7, 8.0");
    exit(1);
}

cli_step('success', "MySQL {$config['mysql-version']}");

switch($version) {
    case 55: 
        append_contents('mysql', "FROM mysql:5.5");
        break;
    case 56: 
        append_contents('mysql', "FROM mysql:5.6");
        break;
    case 57: 
        append_contents('mysql', "FROM mysql:5.7");
        break;
    case 80: 
        append_contents('mysql', "FROM mysql:8.0");
        break;
}

file_put_contents($dockerFile, get_contents('mysql'));


