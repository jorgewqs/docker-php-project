<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este script gera o arquivo docker-nginx.conf para o nginx.
 * É invocado pelo arquivo docker-php-project.php e 
 * fornece a variável $settings, contendo as configurações
 * setadas para a compilação
 */

$config = $settings['nginx'];

$snippetPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'nginx']);
$configFile  = implode(DIRECTORY_SEPARATOR, [getcwd(), 'docker-nginx.conf']);

cli_step('success', "Gerando arquivo de configuração para o Nginx {$config['nginx-version']}");

$nginx_redir                = $settings['nginx']['nginx-config-redir'];
$nginx_redir_target         = $settings['nginx']['nginx-redir-target'];
$nginx_client_max_body_size = $settings['nginx']['nginx-client-max-body-size'];

if (!is_file($nginx_redir_target)) {

    $dir = dirname($nginx_redir_target);
    if ($dir != "") {
        mkdir($dir);
    } 
    $phpindex = "<?php 
    phpinfo();
    ";
    file_put_contents($nginx_redir_target, $phpindex);
}

if (boolval($nginx_redir) === true) {

    $rootDir = dirname($nginx_redir_target);
    $rootDir = ($rootDir != '') ? "/".$rootDir : $rootDir;

    $config = "
        #----------------------------------------------
        # URIs Amigáveis
        # Nesta configuração as URIs são redirecionadas 
        # para o /index.php
        #----------------------------------------------

        server {
            
            listen 80 default;

            client_max_body_size {$nginx_client_max_body_size};

            access_log /var/log/nginx/application.access.log;

            root /var/www/html{$rootDir};
            
            index  index.php index.html index.htm;

            try_files \$uri \$uri/ /index.php?\$query_string;

            # Deny all . files
            location ~ /\. {
                deny all;
            }
            
            # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
            location ~ \.php$ {
                fastcgi_pass php:9000;
                fastcgi_split_path_info ^(.+\.php)(/.*)$;
                fastcgi_index index.php;
                send_timeout 1800;
                fastcgi_read_timeout 1800;
                fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
                fastcgi_param PHP_VALUE \"error_log=/var/log/nginx/application_php_errors.log\";
                fastcgi_buffers 16 16k;
                fastcgi_buffer_size 32k;
                include fastcgi_params;
            }
        }
    ";

} else {

    $config = "
        #----------------------------------------------
        # Acesso direto a arquivos PHP
        # Esta configuração não contempla URIs amigáveis!
        # Ou seja, os arquivos não são redirecionados 
        # para o /index.php mas invocados diretamente.
        #----------------------------------------------

        server {
            
            listen 80 default;

            client_max_body_size {$nginx_client_max_body_size};

            access_log /var/log/nginx/application.access.log;

            root /var/www/html;
            
            index  index.php index.html index.htm;

            # Deny all . files
            location ~ /\. {
                deny all;
            }
            
            # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
            location ~ \.php$ {
                fastcgi_pass php:9000;
                fastcgi_split_path_info ^(.+\.php)(/.*)$;
                fastcgi_index index.php;
                send_timeout 1800;
                fastcgi_read_timeout 1800;
                fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
                fastcgi_param PHP_VALUE \"error_log=/var/log/nginx/application_php_errors.log\";
                fastcgi_buffers 16 16k;
                fastcgi_buffer_size 32k;
                include fastcgi_params;
            }
        }
    ";

}

file_put_contents($configFile, $config);
