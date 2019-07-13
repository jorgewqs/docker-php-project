<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este arquivo gera o arquivo Dockerfile para o NGINX.
 * É invocado pelo arquivo docker-php-project.php e 
 * fornece a variável $settings, contendo as configurações
 * setadas para a compilação
 */

$config = $settings['nginx'];

// $snippetPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'nginx']);
$dockerFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'DockerfileNGINX']);

// VERSÃO
$version = (int) preg_replace('#[^0-9]#', '', $config['nginx-version']);
if (in_array($version, [115,116] ) == false) {
    cli_message('error', "Versão não suportada do nginx: (\"{$config['nginx-version']}\")");
    cli_message('default', "Use uma dessas: 1.15, 1.16");
    exit(1);
}

cli_step('success', "NGINX {$config['nginx-version']}");

switch($version) {
    case 115: 
        append_contents('nginx', "FROM nginx:1.15");
        break;
    case 116: 
        append_contents('nginx', "FROM nginx:1.16");
        break;
}

file_put_contents($dockerFile, get_contents('nginx'));
