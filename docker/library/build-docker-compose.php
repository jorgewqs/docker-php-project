<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este arquivo gera o arquivo docker-compose.yml para o projeto.
 */

$config = $settings['php'];
$extras = $settings['extra']; 

$dockerComposeFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-compose.yml']);

$php_container = $settings['php']['php-container-name'];
$php_port      = $settings['php']['php-port'];

# - \"./docker/scripts/boot-laravel.sh:/usr/local/bin/boot-laravel\"

$phpDepends = "";
if ( (bool) $settings['mysql']['mysql-enabled'] === true) {
    $phpDepends = "
        depends_on:
            - mysql";
}

$php = "
    # Camada da aplicação
    # https://hub.docker.com/_/php
    php:
        container_name: {$php_container}
        build:
            context: ./
            dockerfile: DockerfilePHP
        volumes:
            - \".:/var/www/html\"
            - \"./docker-php.ini:/usr/local/etc/php/conf.d/local.ini\"
        ports:
            - \"{$php_port}:9000\" # mapeada a porta {$php_container} para a porta 9000 {$phpDepends}
        networks:
          - container-network
";

$nginx = "";
if ((bool) $settings['nginx']['nginx-enabled'] == true) {
    
    $nginx_container = $settings['nginx']['nginx-container-name'];
    $nginx_port      = $settings['nginx']['nginx-port'];

    $nginx = "
    # Camada do Web server
    # https://hub.docker.com/_/nginx/
    nginx:
        container_name: {$nginx_container}
        build:
            context: ./
            dockerfile: DockerfileNGINX
        volumes:
            - \"./:/var/www/html\"
            - \"./docker-nginx.conf:/etc/nginx/conf.d/default.conf\"
        ports:
            - \"{$nginx_port}:80\" # mapeada a porta $nginx_port para a porta 80
        depends_on:
            - php
        networks:
            - container-network
    ";

}

$mysql = "";
if ((bool) $settings['mysql']['mysql-enabled'] == true) {
    
    $mysql_container = $settings['mysql']['mysql-container-name'];
    $mysql_port      = $settings['mysql']['mysql-port'];
    $mysql_database  = $settings['mysql']['mysql-dbname'];
    $mysql_user      = $settings['mysql']['mysql-user'];
    $mysql_pass      = $settings['mysql']['mysql-pass'];
    $mysql_rootpass  = $settings['mysql']['mysql-root-pass'];

    $mysql_init_database = $settings['mysql']['mysql-init-database'];
    $mysql_init_path     = $settings['mysql']['mysql-init-database-path'];

    $mysql_init_script = "";
    if (boolval($mysql_init_database) === true) {
        $mysql_init_script = "
            # qualquer arquivo SQL dentro de /{$mysql_init_path} será 
            # executado automaticamente na criação do container
            - \"./{$mysql_init_path}:/docker-entrypoint-initdb.d\"";
    }

    $mysql = "
    # Camada de persistencia
    # https://hub.docker.com/_/mysql
    mysql:
        container_name: {$mysql_container} 
        build:
            context: ./
            dockerfile: DockerfileMYSQL
        volumes:
            - \"db_data:/var/lib/mysql\"
            - \"./docker-mysql.cnf:/etc/mysql/my.cnf\"{$mysql_init_script}
        ports:
            - \"{$mysql_port}:3306\" # mapeada a porta {$mysql_port} para a porta 3306
        environment:
            - MYSQL_HOST={$mysql_container} # o host é o mesmo nome do container
            - MYSQL_PORT=3306
            - MYSQL_ROOT_PASSWORD={$mysql_rootpass}
            - MYSQL_DATABASE={$mysql_database}
            - MYSQL_USER={$mysql_user}
            - MYSQL_PASSWORD={$mysql_pass}
        networks:
            - container-network
    ";
}

$containers = $php . $nginx . $mysql;

$body = "
version: \"3\" 
services:
{$containers}
# Docker Volumes
volumes:
  db_data:
    driver: local

# Docker Networks
networks:
  container-network:
    driver: bridge
";

file_put_contents($dockerComposeFile, $body);
