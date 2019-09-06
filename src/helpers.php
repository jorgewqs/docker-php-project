<?php

/**
 * Libera uma mensagem para o terminal
 * @param string $message
 */
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
    cli_info("Gerando arquivo docker.php".PHP_EOL);

    $source = __DIR__ . DIRECTORY_SEPARATOR . 'docker.php';
    $destiny = getcwd() . '/docker.php';
    copy($source, $destiny);

    if (is_file($destiny)) {
        cli_ok( cli_bold("Arquivo gerado com sucesso!") . PHP_EOL);
    } else {
        cli_error( cli_bold("Não foi possível gerar o arquivo!") . PHP_EOL);
    }
}

function load_project_file()
{
    include getcwd() . '/docker.php';
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


