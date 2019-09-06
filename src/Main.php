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

if ($operation != 'init') {
    check_project_file();
}

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
    
}
