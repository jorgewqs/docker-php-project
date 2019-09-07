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
        load_project_file();
        (new ProjectFactory)->run();
        break;

    case 'down':
        break;

    case 'init':
        generate_project_file();
        break;

    default:
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
