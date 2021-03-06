<?php
namespace Dpp;

class BuildDockerCompose extends Build
{
    protected function getFilename()
    {
        return 'docker-compose.yml';
    }
    
    protected function handle()
    {
        $this->add('version: "3.1"');
        $this->add('services:');
        $this->add(' ');

        $projectDir = path_basename($this->getProjectDir());
        $userConfigDir = str_replace("\n", "", command_exec('echo $HOME')) 
                       . DIRECTORY_SEPARATOR 
                       . '.docker-php-project';

        $containers = Register::getInstance()->all();
        foreach($containers as $name => $config) {

            switch($name) {

                case 'php':
                    $php = new PHP\BuildDockerFile($config);
                    $name = $php->getParam('name', 'php');
                    $workdir = $php->getParam('workdir', '/var/www/html');

                    $this->add('php:', 1);
                    $this->add("container_name: {$name}", 2);
                    $this->add("working_dir: {$workdir}", 2);
                    $this->add("build:", 2);
                        $this->add("context: ./", 3);
                        $this->add("dockerfile: ./{$projectDir}/DockerfilePHP", 3);
                    $this->add("volumes:", 2);
                        $this->add("- ./:{$workdir}", 3);
                        $this->add("- ./{$projectDir}/php.ini:/usr/local/etc/php/conf.d/local.ini", 3);
                        $this->add("- ./{$projectDir}/bashrc:/root/.bashrc", 3);
                        $this->add("- ./{$projectDir}/boot.sh:/root/boot.sh", 3);
                        $this->add("- ~/.bash_history:/root/.bash_history", 3);
                    $this->add(' ');
                    break;

                case 'nginx':
                    $nginx = new NGINX\BuildDockerFile($config);
                    $name = $nginx->getParam('name', 'nginx');
                    $port = $nginx->getParam('port', 80);
                    $workdir = $nginx->getParam('workdir', '/var/www/html');

                    $this->add('webserver:', 1);
                    $this->add("container_name: {$name}", 2);
                    $this->add("working_dir: {$workdir}", 2);
                    $this->add("build:", 2);
                        $this->add("context: ./", 3);
                        $this->add("dockerfile: ./{$projectDir}/DockerfileNGINX", 3);
                    $this->add("volumes:", 2);
                        $this->add("- ./:{$workdir}", 3);
                        $this->add("- ./{$projectDir}/nginx.conf:/etc/nginx/conf.d/default.conf", 3);
                    $this->add("ports:", 2);
                        $this->add("- \"{$port}:80\"", 3);
                    $this->add(' ');
                    break;

                case 'mysql':
                    $mysql = new MYSQL\BuildDockerFile($config);
                    $workdir = $mysql->getParam('workdir', '/var/www/html');
                    $name = $mysql->getParam('name', 'mysql');
                    $port = $mysql->getParam('port', 3306);
                    $dbname = $mysql->getParam('dbname', 'app_database');
                    $dbuser = $mysql->getParam('user', 'dbuser');
                    $dbpass = $mysql->getParam('pass', 'secret');
                    $rootPass = $mysql->getParam('root-pass', 'secret');
                    $initDb = $mysql->getParam('init-database', 'true');
                    $initDbPath = $mysql->getParam('init-database-path', 'boot-database');
                    $dataVolume = $mysql->getParam('data-volume', 'local');

                    $this->add('mysql:', 1);
                    $this->add("container_name: {$name}", 2);
                    $this->add("working_dir: {$workdir}", 2);
                    $this->add("build:", 2);
                        $this->add("context: ./", 3);
                        $this->add("dockerfile: ./{$projectDir}/DockerfileMYSQL", 3);
                    $this->add("volumes:", 2);
                        $this->add("- ./:{$workdir}", 3);
                        $this->add("- ./{$projectDir}/mysql.cnf:/etc/mysql/my.cnf", 3);
                        if ( (bool) $initDb !== false) {
                            $this->add("# qualquer arquivo SQL dentro de /{$initDbPath} será", 3);
                            $this->add("# executado automaticamente na criação do container", 3);
                            $this->add("- ./{$initDbPath}:/docker-entrypoint-initdb.d", 3);
                        }
                        if ($dataVolume == 'shared') {
                            $this->add("# os dados do banco são compartilados com outros projetos", 3);
                            $this->add("- {$userConfigDir}/volumes/shared/mysql:/var/lib/mysql", 3);
                        } else {
                            $this->add("# os dados do banco são exclusivos deste projeto", 3);
                            $this->add("- {$userConfigDir}/volumes/{$name}/mysql:/var/lib/mysql", 3);
                        }
    
                    $this->add("environment:", 2);
                        $this->add("- MYSQL_HOST={$name} # o host é o mesmo nome do container", 3);
                        $this->add("- MYSQL_PORT=3306", 3);
                        $this->add("- MYSQL_ROOT_PASSWORD={$rootPass}", 3);
                        $this->add("- MYSQL_DATABASE={$dbname}", 3);
                        $this->add("- MYSQL_USER={$dbuser}", 3);
                        $this->add("- MYSQL_PASSWORD={$dbpass}", 3);
                    $this->add("ports:", 2);
                        $this->add("- \"{$port}:3306\"", 3);
                    $this->add(' ');
                    break;

            }
        }
    }
}