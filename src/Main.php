<?php
namespace Dpp;

require __DIR__.'/helpers.php';
require __DIR__.'/Register.php';
require __DIR__.'/Module.php';
require __DIR__.'/ProjectFactory.php';
require __DIR__.'/Build.php';
require __DIR__.'/BuildDockerFile.php';
require __DIR__.'/BuildDockerCompose.php';
require __DIR__.'/PHP/BuildDockerFile.php';
require __DIR__.'/NGINX/BuildDockerFile.php';
require __DIR__.'/MYSQL/BuildDockerFile.php';

check_php_version();

$operation = $argv[1] ?? null;
$task      = $argv[2] ?? null;

switch($operation) {
    case 'up':
        if (load_project_file() == false){
            cli_error('O arquivo "docker.php" não foi encontrado neste diretório' . PHP_EOL);
            cli_out('Use "php-project ini" para gerá-lo.' . PHP_EOL);
            exit(1);
        }
        (new ProjectFactory)->run();
        break;

    case 'down':
        break;

    case 'init':
        generate_project_file();
        break;

    default:
        show_help();
    
}
