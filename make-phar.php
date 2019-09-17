#!/usr/bin/php
<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este script executa o dpp em modo de desenvolvimento.
 * As seguintes tarefas contecem cada vez que dpp-dev é invocado:
 *  1. um novo phar é compilado
 *  2. um novo link é efetuado para a aplicação de dev
 * 
 * $ dpp-test up
 * 
 */

define('PATH_ROOT', __DIR__);
define('PATH_DIST', PATH_ROOT . DIRECTORY_SEPARATOR . 'dist');
define('PATH_SRC', PATH_ROOT . DIRECTORY_SEPARATOR . 'src');
define('PHAR_NAME', 'php-project.phar');

require 'src/helpers.php';

check_php_version();

if ((bool) ini_get('phar.readonly') == true || ini_get('phar.readonly') == 'On') {
    $message = "A configuração do PHP não permite criação de arquivos PHAR" . PHP_EOL 
             . "Por favor, sete phar.readonly para Off";
    cli_error($message, true);
}

$phar_file = PATH_DIST . DIRECTORY_SEPARATOR . PHAR_NAME;
if (file_exists($phar_file)) {
    unlink($phar_file);
}
if (file_exists($phar_file . '.gz')) {
    unlink($phar_file . '.gz');
}

$p = new Phar($phar_file);

// creating our library using whole directory  
$p->buildFromDirectory(PATH_SRC);

// pointing main file which requires all classes  
$p->setDefaultStub('Main.php', '/Main.php');

// plus - compressing it into gzip  
$p->compress(Phar::GZ);

cli_ok("Arquivo " . PHAR_NAME . " criado com sucesso" . PHP_EOL);