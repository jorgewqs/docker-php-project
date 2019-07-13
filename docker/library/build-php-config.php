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

$snippetPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'php']);
$configFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-php.ini']);

cli_step('success', "Gerando arquivo de configuração para o PHP {$config['php-version']}");

append_contents('php-config', "date.timezone = America/Sao_Paulo");

append_contents('php-config', "[PHP]");
append_contents('php-config', "max_execution_time = 600");
append_contents('php-config', "memory_limit = 512M");
append_contents('php-config', "post_max_size = 512M");
append_contents('php-config', "upload_max_filesize = 512M");

append_contents('php-config', "[Session]");
append_contents('php-config', "session.cookie_httponly = 1");
append_contents('php-config', "session.hash_function = 1");
append_contents('php-config', "session.save_path = \"/sessions\"");
append_contents('php-config', "session.use_strict_mode = 1");

file_put_contents($configFile, get_contents('php-config'));
