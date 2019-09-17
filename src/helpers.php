<?php

function url_get_contents ($url) 
{
    if (! function_exists('curl_init') ) {
        die( 'The cURL library is not installed.' );
    }

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $output = curl_exec( $ch );
    curl_close( $ch );
    return $output;
}

function path_get_contents($path)
{
    return file_get_contents($path);
}

function path_basename($path)
{
    return basename($path);
}

function version()
{
    return path_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'version.txt');
}

function cli_out($message)
{
    fwrite(STDOUT, $message);
}

function cli_exec($command, $quiet = false)
{
    if ($quiet == true) {
        shell_exec($command);
    } else {
        cli_out( shell_exec($command) );
    }
}

/**
 * Gera uma string colorida para exibir no terminal
 * @param array $color red|green|yellow|blue
 * @param string $text
 * @return string
 */
function cli_color($color, $text)
{
    $string = '';

    switch($color) {
        case 'yellow';
            $string .= "\e[33m";
            break;

        case 'red';
            $string .= "\033[0;31m";
            break;

        case 'green';
            $string .= "\033[0;32m";
            break;

        case 'blue';
            $string .= "\e[34m";
            break;
    }

    $string .= $text;

    // cor normal
    $string .= "\033[0m";

    return $string;
}

function cli_bold($string)
{
    return "\e[1m{$string}\033[0m";
}

function cli_info($message, $shutdown = false)
{
    cli_out( cli_color('blue', "→ " . $message) );
    if ($shutdown == true) {
        exit(0);
    }
}

function cli_error($message, $shutdown = false)
{
    cli_out( cli_color('red', "✖ ". $message) );
    if ($shutdown == true) {
        exit(1);
    }
}

function cli_warn($message, $shutdown = false)
{
    cli_out( cli_color('yellow', "⦁ " . $message) );
    if ($shutdown == true) {
        exit(0);
    }
}

function cli_ok($message, $shutdown = false)
{
    cli_out( cli_color('green', "✔ " . $message) );
    if ($shutdown == true) {
        exit(0);
    }
}

function cli_step_run($action, $message = '')
{
    $icon = cli_color('green', "✈︎");
    $message = cli_color('green', $message);
    cli_out($icon . " " . $action . " " . $message . PHP_EOL);
}

function cli_step_echo($action, $message = '')
{
    $icon = cli_color('blue', " ");
    $message = cli_color('blue', $message);
    cli_out($icon . " " . $action . " " . $message . PHP_EOL);
}

function cli_step_ok($action, $message = '')
{
    $icon = cli_color('green', "✔");
    $message = cli_color('green', $message);
    cli_out($icon . " " . $action . " " . $message . PHP_EOL);
}

function cli_step_info($action, $message = '')
{
    $icon = cli_color('blue', "→");
    $message = cli_color('blue', $message);
    cli_out($icon . " " . $action . " " . $message . PHP_EOL);
}

function cli_step_error($action, $message = '')
{
    $icon = cli_color('red', "✖");
    $message = cli_color('yellow', $message);
    cli_out($icon . " " . $action . " " . $message . PHP_EOL);
}


function check_php_version()
{
    if (!defined('PHP_MAJOR_VERSION') || PHP_MAJOR_VERSION < 7) {
        $message = 'Atualize para o PHP7' . PHP_EOL 
                 . 'Docker PHP Project 2.x suporta apenas PHP7 ou superior.';
        cli_error($message, true);
    }
}

function check_project_file()
{
    $projectFile = getcwd() . '/docker.php';
    if (is_file($projectFile) == false) {
        cli_warn('Este projeto não foi iniciado ainda.' . PHP_EOL);
        cli_out('Use "php-project init"' . PHP_EOL, true);
    }
}

function generate_project_file()
{
    cli_step_info('Gerando arquivo', 'docker.php');

    $source = __DIR__ . DIRECTORY_SEPARATOR . 'docker.php';
    $destiny = getcwd() . '/docker.php';

    $basename = \Dpp\Register::getInstance()->getDefaultParam('basename');
    $contents = path_get_contents($source);
    $contents = str_replace("->param('---name---', 'app')", "->param('name', 'app_{$basename}')", $contents);
    $contents = str_replace("->param('---name---', 'webserver')", "->param('name', 'webserver_{$basename}')", $contents);
    $contents = str_replace("->param('---name---', 'database')", "->param('name', 'database_{$basename}')", $contents);
    file_put_contents($destiny, $contents);

    if (is_file($destiny)) {
        cli_step_ok('OK', '');
    } else {
        cli_step_error('Erro:', 'Não foi possível gerar o arquivo!');
    }
}

function load_project_file()
{
    $projectFile = getcwd() . '/docker.php';
    if (! is_file($projectFile)) {
        return false;
    }
    include getcwd() . '/docker.php';
    return true;
}

function show_help()
{
    $sp = "  ";
    cli_out("Docker PHP Project " . cli_color('green', version()));
    cli_out(PHP_EOL);
    cli_out( cli_color('yellow', "Usage:") );
    cli_out(PHP_EOL);
    cli_out( "${sp}php-project [options] [arguments]" );
    cli_out(PHP_EOL);
    cli_out(PHP_EOL);
    
    cli_out( cli_color('yellow', "Options:") );
    cli_out(PHP_EOL);
    cli_out( cli_color('green', "${sp}init      ") . "Inicia um novo projeto no diretório atual" );
    cli_out(PHP_EOL);
    cli_out( cli_color('green', "${sp}up        ") . "Constrói e sobe os containers do projeto" );
    cli_out(PHP_EOL);
    cli_out( cli_color('green', "${sp}down      ") . "Para a execução dos contaners" );
    cli_out(PHP_EOL);
}

function set($param, $value)
{
    defaults()->param($param, $value);
}

function defaults()
{
    return (new Dpp\Module('defaults'));
}

function php($version)
{
    return (new Dpp\Module('php'))->version($version);
}

function nginx($version)
{
    return (new Dpp\Module('nginx'))->version($version);
}

function mysql($version)
{
    return (new Dpp\Module('mysql'))->version($version);
}

function task($name)
{
    return (new Dpp\Task($name));
}

