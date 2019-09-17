<?php
namespace Dpp;

$libs = [
    'helpers.php',
    'Register.php',
    'Module.php',
    'ProjectFactory.php',
    'ProjectTasks.php',
    'Build.php',
    'BuildDockerFile.php',
    'BuildDockerCompose.php',
    'Task.php',
    'PHP/BuildDockerFile.php',
    'NGINX/BuildDockerFile.php',
    'MYSQL/BuildDockerFile.php',
];
foreach($libs as $item) {
    require $item;
}

check_php_version();

$operation = $argv[1] ?? null;
$task      = $argv[2] ?? null;

// seta parâmetros padrões
set('workdir', '/project');
set('basename', path_basename(getcwd()));

if (in_array($operation, ['up', 'tasks', 'app']) && load_project_file() == false) {
    cli_error('O arquivo "docker.php" não foi encontrado neste diretório' . PHP_EOL);
    cli_out('Use "php-project ini" para gerá-lo.' . PHP_EOL);
    return;
}

switch($operation) {
    case 'up':
        (new ProjectFactory)->run();
        break;

    case 'down':
        break;

    case 'init':
        generate_project_file();
        break;

    case 'tasks':
        (new ProjectTasks)->run();
        break;

    case 'app':
        return Register::getInstance()->getParam('php', 'name');
        break;

    default:
        show_help();
    
}
