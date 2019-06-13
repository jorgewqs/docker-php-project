<?php

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este script efetua o processo de build para cada extensão do PHP de 
 * forma sequencial e separada para detectar falhas de compilação.
 * 
 * Caso um erro seja detectado ele será reportado ao término da 
 * execução de todas as compilações.
 * 
 * Para usar é preciso especificar a versão do PHP a ser testada 
 * como argumento do programa:
 * 
 * $ cd docker/php
 * $ php run-tests.php 5.6
 * 
 */

include 'cli-functions.php';

if (isset($argv[1]) == false) {
    cli_message('error', "É obrigatório especificar a versão do PHP");
    exit;
}

$tests_path = implode(DIRECTORY_SEPARATOR, [__DIR__, 'tests']);
shell_exec("mkdir {$tests_path}");

$version = preg_replace('#[^0-9]#', '', $argv[1]);
if (in_array($version, [56,70,71,72,73] ) == false) {
    cli_message('error', "Versão não suportada do PHP: (\"{$argv[1]}\")");
    cli_message('default', "Use uma dessas: 5.6, 7.0, 7.1, 7.2, 7.3");
    exit;
}
$version = $version{0} . "." . $version{1};

// Analisa o arquivo de configuração original
// Trata o arquivo de configuração
$from_file = realpath('image-config.ini');
if (file_exists($from_file) == false) {
    cli_message('error', "O arquivo de configuração \"$from\" não foi encontrado!");
    exit;
}

$config = @parse_ini_file($from_file, true);
if ($config === false) {
    cli_message('error', "O arquivo de configuração \"$from\" é inválido!");
    exit;
}

// Gera uma lista de configurações para cada extensão
$files = [];
foreach($config['extensions'] as $module => $status) {

    $files[$module] = $config;

    foreach($files[$module]['extensions'] as $k => $v) {
        if ($k != $module) {
            $files[$module]['extensions'][$k] = 0;
        } else {
            $files[$module]['extensions'][$k] = 1;
        }
    }

    foreach($files[$module]['extra'] as $k => $v) {
        $files[$module]['extra'][$k] = 0;
    }

}

// Gera uma lista de configurações para cada ferramenta
foreach($config['extra'] as $tool => $status) {

    $files[$tool] = $config;

    foreach($files[$tool]['extensions'] as $k => $v) {
        $files[$tool]['extensions'][$k] = 0;
    }

    foreach($files[$tool]['extra'] as $k => $v) {
        if ($k != $tool) {
            $files[$tool]['extra'][$k] = 0;
        } else {
            $files[$tool]['extra'][$k] = 1;
        }
    }

}

cli_exec("sudo docker-compose down");
// cli_exec("sudo docker container rm prune");
// cli_exec("sudo docker image rm docker-php-project_php");
// cli_exec("sudo docker image rm php:{$version}-fpm");
cli_exec("sudo docker system prune -a");


$success = [];
$skips = [];
$errors = [];

// Compila as imagens para cada extensão/ferramenta
foreach($files as $name =>  $item) {

    cli_message('info', "-----------------------------------------------");
    cli_message('info', "");
    cli_message('info', "Testando o PHP {$version} com " . cli_color('green', $name));
    cli_message('info', "Imagem limpa do PHP + extensão/ferramenta");
    cli_message('info', "");
    cli_message('info', "-----------------------------------------------");

    $content = "php_version = {$version}\n";
    $content .= "\n[extensions]\n\n";
    foreach($item['extensions'] as $module => $status) {
        $content .= "{$module} = $status\n";
    }
    $content .= "\n[extra]\n\n";
    foreach($item['extra'] as $tool => $status) {
        $content .= "{$tool} = $status\n";
    }

    // gera o arquivo de configuração
    $config_file = implode(DIRECTORY_SEPARATOR, [$tests_path,"config-{$name}.ini"]);
    file_put_contents($config_file, $content);

    // gera o dockerfile
    $command = implode(DIRECTORY_SEPARATOR, [__DIR__,'build-dockerfile.php']);
    $docker_file = implode(DIRECTORY_SEPARATOR, [$tests_path, "Dockerfile-{$name}"]);
    $result = cli_exec("php {$command} -c $config_file -o {$docker_file}");

    // Se o item for pulado, não faz sentido compilar a imagem
    if (strpos($result['output'], 'PULAR') !== false) {

        $skips[] = [
            'name' => $name,
            'message' => $result['output']
        ];

        cli_message('default', "");
        cli_exec("rm -f {$config_file}");
        cli_exec("rm -f {$docker_file}");
        continue;
    }

    $response = cli_exec("docker build . -f {$docker_file}");
    
    $error_pos = strpos($response['output'], 'error:');
    if ($error_pos !== false) {

        $errors[] = [
            'name' => $name,
            'message' => substr($response['output'], $error_pos)
        ];

    } elseif(strpos($response['output'], 'Successfully built') !== false) {

        $success[] = [
            'name' => $name,
            'message' => "PHP com $name compilado com sucesso"
        ];

        cli_message('success', "-----------------------------------------------");
        cli_message('success', "");
        cli_message('success', "PHP com $name compilado com sucesso");
        cli_message('success', "");

    }

    // Remove os arquivos de teste
    cli_exec("rm -f {$config_file}");
    cli_exec("rm -f {$docker_file}");

}

cli_exec("rmdir {$tests_path}");

if (count($skips)>0) {

    cli_message('info', "-----------------------------------------------");
    cli_message('info', "");
    cli_message('info', "Os seguintes componentes foram pulados:");
    cli_message('info', "");

    $count = 1;
    foreach($skips as $item) {

        $name = $item['name'];
        $message = $item['message'];
        cli_message('info', "-----------------------------------------------");
        cli_message('info', "$count) Ao instalar $name:");
        cli_message('info', "-----------------------------------------------");
        // Remove a coloração original das mensagens
        cli_message('default', preg_replace('/[[:cntrl:]]\[[0-9]{1,3}m/','', $message));
        $count++;
    }

}

if (count($errors)>0) {

    cli_message('error', "-----------------------------------------------");
    cli_message('error', "");
    cli_message('error', "Aconteceram erros durante o processo de testes");
    cli_message('error', "As seguintes mensagens foram devolvidas");
    cli_message('error', "");
    
    $count = 1;
    foreach($errors as $item) {

        $name = $item['name'];
        $message = $item['message'];
        cli_message('error', "-----------------------------------------------");
        cli_message('error', "$count) Ao instalar $name:");
        cli_message('error', "-----------------------------------------------");
        // Remove a coloração original dos erros
        cli_message('default', preg_replace('/[[:cntrl:]]\[[0-9]{1,3}m/','', $message));
        $count++;
    }

} else {

    cli_message('success', "-----------------------------------------------");
    cli_message('success', "");
    cli_message('success', "PARABÉNS!");
    cli_message('success', "Parece que tudo foi compilado com sucesso :)");
    cli_message('success', "");
}

if (count($success)>0) {

    cli_message('success', "-----------------------------------------------");
    cli_message('success', "Instalados e compilados com sucesso:");
    cli_message('success', "-----------------------------------------------");

    foreach($success as $item) {
        $name = $item['name'];
        $message = $item['message'];
        cli_step('success', "$name");
    }

    cli_message('success', "-----------------------------------------------");
}

