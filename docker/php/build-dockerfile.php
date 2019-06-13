<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este script gera um arquivo Dockerfile de acordo com as especificações
 * de configuração fornecidas na linha de comando.
 * 
 * Por exemplo, para usar o arquivo de configuração 'image-config.ini' 
 * e gerar um arquivo chamado 'MeuDockerfile':
 * 
 * $ cd docker/php
 * $ php build-dockerfile.php -c image-config.ini -o MeuDockerfile
 * 
 */

include 'cli-functions.php';

function append_string($string)
{
    global $dockerfile;

    if($dockerfile == null) {
        $dockerfile = "";
    }

    $dockerfile .= $string . "\n";
}

function get_dockefile()
{
    global $dockerfile;
    return $dockerfile;
}



// ARGUMENTOS
// Normaliza os argumentos para verificação posterior
$from = null;
$to = null;

if ($argv[1] == '-c') {
    $from = $argv[2];
}
if ($argv[3] == '-c') {
    $from = $argv[4];
}

if ($argv[1] == '-o') {
    $to = $argv[2];
}
if ($argv[3] == '-o') {
    $to = $argv[4];
}


// HELP
// Esxibe a mensagem de ajuda
if (isset($argv[1]) == false || $argv[1] == '--help' || $argv[1] == '-h'|| $from == null) {


    cli_message('info', "-----------------------------------------------");
    cli_out("Build PHP Dockerfile\n");
    cli_out("---------------\n");
    cli_out("Modo de usar: php buid-dockerfile -c configfile -o dockerfile \n");
    cli_out("Por exemplo: php buid-dockerfile -c image-config.ini -o Dockerfile-php73 \n");
    cli_message('info', "-----------------------------------------------");
    exit;

}

// CONFIG FILE
// Trata o arquivo de configuração
$from_file = realpath($from);
if (file_exists($from_file) == false) {
    cli_message('error', "O arquivo de configuração \"$from\" não foi encontrado!");
    exit;
}

$config = @parse_ini_file($from_file, true);
if ($config === false) {
    cli_message('error', "O arquivo de configuração \"$from\" é inválido!");
    exit;
}

// Remove comentarios
foreach($config as $section => $params) {

    if (is_array($params)) {
        foreach($params as $name => $value) {
            if (strpos($value, "#") !== false) {
                $value_split = explode('#', $value);
                $config[$section][$name] = $value_split[0];
            }
        }
    } else {
        if (strpos($params, "#") !== false) {
            $value_split = explode('#', $params);
            $config[$section] = $value_split[0];
        }
    }

}

// DESTINO
if ($to == null) {
    $to = __DIR__ . DIRECTORY_SEPARATOR . 'Dockerfile';
}

// VERSÃO
$config['php_version'] = trim($config['php_version']);
$version = (int) preg_replace('#[^0-9]#', '', $config['php_version']);
if (in_array($version, [56,70,71,72,73] ) == false) {
    cli_message('error', "Versão não suportada do PHP: (\"{$config['php_version']}\")");
    cli_message('default', "Use uma dessas: 5.6, 7.0, 7.1, 7.2, 7.3");
    exit;
}
switch($version) {
    case 56: 
        append_string("FROM php:5.6-fpm");
        break;
    case 70: 
        append_string("FROM php:7.0-fpm");
        break;
    case 71: 
        append_string("FROM php:7.1-fpm");
        break;
    case 72: 
        append_string("FROM php:7.2-fpm");
        break;
    case 73: 
        append_string("FROM php:7.3-fpm");
        break;
}

append_string("");
append_string("");

append_string("##############################################");
append_string("# Preparação");
append_string("##############################################");

append_string("RUN apt-get update;");

append_string("");
append_string("# Muda o UID e o GID do usuario www-data para obter os privilégios do host");
append_string("RUN usermod -u 999 www-data && groupmod -g 999 www-data;");



// EXTENSÕES
if (isset($config['extensions']) == true) {

    append_string("");
    append_string("##############################################");
    append_string("# Módulos do PHP");
    append_string("##############################################");
    append_string("");

    foreach($config['extensions'] as $module => $status) {

        if ($status == true) {

            if ($version <= 56 && $module == 'pdo-oci8') {
                cli_step('skip', "Módulo $module");
                cli_message('warning', "A extensão pdo-oci8 não está disponível para o PHP 5.6!");
                continue;
            }

            if ($version > 56 && $module == 'pdo-mssql') {
                cli_step('skip', "Módulo $module");
                cli_message('warning', "A partir do PHP 7.0, a extensão mssql foi depreciada e não está mais disponivel!");
                continue;
            }

            if ($version > 56 && $module == 'mysql') {
                cli_step('skip', "Módulo $module");
                cli_message('warning', "A partir do PHP 7.0, a extensão mysql foi depreciada e não está mais disponivel!");
                continue;
            }

            if ($version >= 72 && $module == 'mcrypt') {
                cli_step('skip', "Módulo $module");
                cli_message('warning', "A partir do PHP 7.2, a extensão mcrypt foi depreciada e não está mais disponivel!");
                continue;
            }

            $snippet = implode(DIRECTORY_SEPARATOR, [__DIR__,"php{$version}", $module]);

            if (file_exists($snippet) === false ) {
                cli_step('warning', "Módulo $module");
                cli_message('warning', "A extensão $module não está disponível para a configuração do PHP {$config['php_version']}!");
                continue;
            } else {
                $contents = file_get_contents($snippet);
            }

            if (strpos($contents, 'TODO') !== false) {
                cli_step('skip', "Módulo $module");
                cli_message('warning', "A extensão $module ainda não está pronta para uso!");
            } else {
                append_string($contents);
                cli_step('success', "Módulo $module");

                switch($module){
                    case 'pdo-oci8':
                        cli_message('warning', "A Oracle não disponibiliza um meio de fazer o download automaticamente!");
                        cli_message('warning', "Por isso, o módulo é instalado com base em pacotes de terceiros!");
                        break;
                    case 'imagick':
                        cli_message('warning', "O imagemagick exige grande espaço em disco para ser instalado.");
                        cli_message('warning', "Talvez a extensão GD seja uma alternativa melhor!");
                        break;
                }
            }
        }
    }
}

// FERRAMENTAS EXTRAS
if (isset($config['extra']) == true) {

    append_string("");
    append_string("##############################################");
    append_string("# Ferramentas adicionais");
    append_string("##############################################");
    append_string("");

    foreach($config['extra'] as $tool => $status) {
        if ($status == true) {
            $snippet = implode(DIRECTORY_SEPARATOR, [__DIR__,"extra", $tool]);
            append_string(file_get_contents($snippet));
            cli_step('success', "Ferramenta $tool");
        } 
    }
}

append_string("");
append_string("##############################################");
append_string("# Limpeza final");
append_string("##############################################");
append_string("");

append_string("RUN rm -rf /var/lib/apt/lists/*;");
append_string("RUN apt-get autoremove -y;");

// append_string("COPY docker-entrypoint.sh /docker-entrypoint.sh");
// append_string('ENTRYPOINT ["/docker-entrypoint.sh"]');

file_put_contents($to, get_dockefile());
