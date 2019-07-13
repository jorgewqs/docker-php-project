<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este arquivo gera o arquivo Dockerfile para o PHP.
 * É invocado pelo arquivo docker-php-project.php e 
 * fornece a variável $settings, contendo as configurações
 * setadas para a compilação
 */

$config = $settings['php'];
$extras = $settings['extra'];

$snippetPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'php']);
$dockerFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'DockerfilePHP']);

// VERSÃO
$version = (int) preg_replace('#[^0-9]#', '', $config['php-version']);
if (in_array($version, [56,70,71,72,73] ) == false) {
    cli_message('error', "Versão não suportada do PHP: (\"{$config['php-version']}\")");
    cli_message('default', "Use uma dessas: 5.6, 7.0, 7.1, 7.2, 7.3");
    exit(1);
}

cli_step('success', "PHP {$config['php-version']}");

switch($version) {
    case 56: 
        append_contents('php', "FROM php:5.6-fpm");
        break;
    case 70: 
        append_contents('php', "FROM php:7.0-fpm");
        break;
    case 71: 
        append_contents('php', "FROM php:7.1-fpm");
        break;
    case 72: 
        append_contents('php', "FROM php:7.2-fpm");
        break;
    case 73: 
        append_contents('php', "FROM php:7.3-fpm");
        break;
}

append_contents('php', "");
append_contents('php', "");

append_contents('php', "##############################################");
append_contents('php', "# Preparação");
append_contents('php', "##############################################");

append_contents('php', "RUN apt-get update;");

append_contents('php', "");
append_contents('php', "# Para corrigir as mensagens de interface do Debian");
append_contents('php', "ARG DEBIAN_FRONTEND=noninteractive");

// EXTENSÕES
append_contents('php', "");
append_contents('php', "##############################################");
append_contents('php', "# Módulos do PHP");
append_contents('php', "##############################################");
append_contents('php', "");

foreach($config as $module => $status) {

    $module = substr($module, 4);
    if (in_array($module, ['version', 'container-name', 'port'])) {
        continue;
    }

    if (boolval($status) === true) {

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

        $snippet = implode(DIRECTORY_SEPARATOR, [$snippetPath, "php{$version}", $module]);

        if (file_exists($snippet) === false ) {
            cli_step('warning', "Módulo $module");
            cli_message('warning', "A extensão $module não está disponível para a configuração do PHP {$version}!");
            continue;
        } else {
            $contents = file_get_contents($snippet);
        }

        if (strpos($contents, 'TODO') !== false) {
            cli_step('skip', "Módulo $module");
            cli_message('warning', "A extensão $module ainda não está pronta para uso!");
        } else {
            append_contents('php', $contents);
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
    } else {
        cli_step('skip', "Módulo $module");
    }
}

// FERRAMENTAS EXTRAS
append_contents('php', "");
append_contents('php', "##############################################");
append_contents('php', "# Ferramentas adicionais");
append_contents('php', "##############################################");
append_contents('php', "");

foreach($extras as $tool => $status) {
    if ($status == true) {
        $snippet = implode(DIRECTORY_SEPARATOR, [$snippetPath, 'extra', $tool]);
        append_contents('php', file_get_contents($snippet));
        cli_step('success', "Ferramenta $tool");
    } 
}

append_contents('php', "");
append_contents('php', "##############################################");
append_contents('php', "# Outras configurações");
append_contents('php', "##############################################");
append_contents('php', "");
append_contents('php', "RUN mkdir /sessions;"); 
append_contents('php', "RUN chmod 777 /sessions;");
# append_contents('php', "RUN mkdir -p /var/nginx/client_body_temp;");
append_contents('php', "RUN chmod 777 /sessions;");

append_contents('php', "");
append_contents('php', "RUN usermod -u 1000 www-data;");

append_contents('php', "");
append_contents('php', "##############################################");
append_contents('php', "# Limpeza final");
append_contents('php', "##############################################");
append_contents('php', "");

append_contents('php', "RUN rm -rf /var/lib/apt/lists/*;");
append_contents('php', "RUN apt-get autoremove -y;");
append_contents('php', "# Adiciona suporte a acentos"); 
append_contents('php', "ENV LANG C.UTF-8");

append_contents('php', "EXPOSE 9000"); // levanta a porta do fastcgi

// append_contents('php', "COPY docker-entrypoint.sh /docker-entrypoint.sh");
// append_contents('php', 'ENTRYPOINT ["/docker-entrypoint.sh"]');

file_put_contents($dockerFile, get_contents('php'));
