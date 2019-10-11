<?php
namespace Dpp;

$libs = [
    'helpers.php',
    'Register.php',
    'Module.php',
    'ProjectFactory.php',
    'ProjectTasks.php',
    'SetupConfig.php',
    'Build.php',
    'BuildDockerFile.php',
    'BuildDockerCompose.php',
    'Task.php',
    'PHP/BuildBoot.php',
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


$basename = path_basename(getcwd());
$basename = str_replace('-', '_', strtolower($basename));
$basename = preg_replace('[^a-zA-Z0-9\_]', '', $basename);

// seta parâmetros padrões
set('workdir', '/app');
set('basename', $basename);

if (in_array($operation, ['up', 'tasks', 'app']) && load_project_file() == false) {
    cli_error('O arquivo "docker.php" não foi encontrado neste diretório' . PHP_EOL);
    cli_out('Use "php-project init" para gerá-lo.' . PHP_EOL);
    return;
}

switch($operation) {
    case 'setup-config':
        (new SetupConfig)->run();
        break;

    case 'up':
        (new ProjectFactory)->run();
        break;

    case 'init':
        generate_project_file();
        break;

    case 'tasks':
        (new ProjectTasks)->run();
        break;

    case 'app':
        echo Register::getInstance()->getParam('php', 'name');
        break;
    
    case 'bash':
    case 'down':
    case 'reload':
        break;

    default:
        show_help();
    
}
