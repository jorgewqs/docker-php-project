<?php
namespace Dpp;

class ProjectFactory
{
    private $projectDir = null;

    public function __construct()
    {
        $this->projectDir = getcwd() // diretório onde o script está sendo executado
            . '/.docker-project'; // diretóio temporário
    }

    public function run()
    {
        $this->makeDir();

        cli_step_run('Subindo containers para ', '"' . Register::getInstance()->getDefaultParam('basename') . '"');

        $containers = Register::getInstance()->all();

        $containersUp = [];
        foreach($containers as $name => $config) {

            if ($name === 'tasks') {
                continue;
            }

            cli_step_info('Processando configurações', strtoupper($name));

            $className = "\\Dpp\\" . strtoupper($name) . '\\BuildDockerFile';
            if ( class_exists($className) ) {
                (new $className($config))
                    ->setProjectDir($this->projectDir)
                    ->save($this->projectDir);
            }

            // informações para exibir ao usuário
            $name = $config['params']['name'];
            $port = $config['params']['port'] ?? null;
            $containersUp[$name] = [
                'port' => $port
            ];
        }
        
        cli_step_ok('Analisando', 'docker-compose.yaml');
        (new \Dpp\BuildDockerCompose(null))
            ->setProjectDir($this->projectDir)
            ->save(path_dirname($this->projectDir));

        foreach($containersUp as $name => $config) {
            $container = $config['port'] == null 
                ? "{$name}" 
                : "{$name}:{$port}";
            cli_step_info("Container", $container);
        }

        // TODO
        // Verificar se o container já existe e perguntar se usuario quer matá-lo
        // ...
        
        // $this->removeFiles();

        return $this;
    }

    private function makeDir()
    {
        $parent = path_dirname($this->projectDir);
        if (\is_writable($parent) == false) {
            cli_error("Não há permissão para escrever no diretório $parent", true);
        }

        if (has_dir($this->projectDir) == false) {
            make_dir($this->projectDir, 0777, true);
        }
    }

    // private function removeFiles()
    // {
    //     shell_exec("rm -Rf {$this->projectDir}");
    //     // \rmdir($this->projectDir);

    //     $parentDir = path_dirname($this->projectDir);
    //     remove_file($parentDir . DIRECTORY_SEPARATOR . 'docker-compose.yaml');
    // }

}