#!/usr/bin/php
<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este script gera a estrutura para utilização do docker em qualquer projeto PHP.
 * Basta existir um arquivo "docker-project.ini" com as especificações necessárias 
 * no diretório root do projeto.
 * 
 * $ php docker-project.php
 * 
 */

include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'cli-functions.php']);
include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'cli-contents.php']);

// HELP
// Exibe a mensagem de ajuda
if (isset($argv[1]) && ($argv[1] == '--help' || $argv[1] == '-h')) {


    cli_message('info', "-----------------------------------------------");
    cli_out("Docker PHP Project\n");
    cli_out("---------------\n");
    cli_out("Modo de usar: docker-php-project \n");
    cli_message('info', "-----------------------------------------------");
    exit;

}

if (isset($argv[1]) && $argv[1] == '--create') {
    $controlFile = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-project.ini']);
    file_put_contents($controlFile, file_get_contents('docker-project.ini'));
    cli_message('success', "Arquivo docker-project.ini gerado com sucesso");
    exit(0);
}

if (isset($argv[1]) && $argv[1] == '--new') {
    $controlFile = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-project.ini']);
    file_put_contents($controlFile, file_get_contents('docker-project.ini'));
    cli_message('success', "Arquivo docker-project.ini gerado com sucesso");
    cli_message('default', "gerando um novo projeto docker");
}

// Arquivo de controle
$controlFile = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-project.ini']);
if (! is_file($controlFile)) {

    cli_message('error', 'Operação Abortada!');
    cli_message('default', 'Não foi encontrado um arquivo "docker-project.ini"');
    cli_message('default', 'Para gerar um arquivo padrão, use "docker-php-projet --create"');
    exit(1);
}

$custom = @parse_ini_file($controlFile, true);
if ($custom === false) {
    cli_message('error', "O arquivo de configuração \"$controlFile\" é inválido!");
    cli_message('error', error_get_last()['message']);
    exit(1);
}
// Remove comentarios
foreach($custom as $section => $params) {

    if (is_array($params)) {
        foreach($params as $name => $value) {
            if (strpos($value, "#") !== false) {
                $valueSplit = explode('#', $value);
                $custom[$section][$name] = $valueSplit[0];
            }
        }
    } else {
        if (strpos($params, "#") !== false) {
            $valueSplit = explode('#', $params);
            $custom[$section] = $valueSplit[0];
        }
    }
}

$defaultFile = implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker-project.ini']);
$defaults = @parse_ini_file($defaultFile, true);
if ($defaults === false) {
    cli_message('error', "O arquivo padrão de configuração \"$defaultFile\" é inválido!");
    cli_message('error', error_get_last()['message']);
    exit(1);
}

// Remove comentarios
foreach($defaults as $section => $params) {

    if (is_array($params)) {
        foreach($params as $name => $value) {
            if (strpos($value, "#") !== false) {
                $valueSplit = explode('#', $value);
                $defaults[$section][$name] = $valueSplit[0];
            }
        }
    } else {
        if (strpos($params, "#") !== false) {
            $valueSplit = explode('#', $params);
            $defaults[$section] = $valueSplit[0];
        }
    }
}

// Normalização
$settings = [];
foreach($defaults as $section => $list){
    $settings[$section] = [];
    foreach($list as $name => $value){
        $settings[$section][$name] = trim($custom[$section][$name] ?? $value);
    }
}

cli_message('default', "------------------------------------------------");
include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-php-dockerfile.php']);
include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-php-config.php']);

if ((bool) $settings['nginx']['nginx-enabled'] == true) {
    cli_message('default', "------------------------------------------------");
    include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-nginx-dockerfile.php']);
    include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-nginx-config.php']);
}

if ((bool) $settings['mysql']['mysql-enabled'] == true) {
    cli_message('default', "------------------------------------------------");
    include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-mysql-dockerfile.php']);
    include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-mysql-config.php']);
}

include implode(DIRECTORY_SEPARATOR, [__DIR__, 'docker', 'library', 'build-docker-compose.php']);

