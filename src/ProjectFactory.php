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

        cli_step_ok("✈︎", 'Subindo containers', Register::getInstance()->getDefaultParam('basename'));

        $containers = Register::getInstance()->all();

        foreach($containers as $name => $config) {

            if ($name === 'tasks') {
                continue;
            }

            cli_step_info("→", 'Processando configurações', strtoupper($name));

            $className = "\\Dpp\\" . strtoupper($name) . '\\BuildDockerFile';
            if ( class_exists($className) ) {
                (new $className($config))
                    ->setProjectDir($this->projectDir)
                    ->save($this->projectDir);
            }
        }

        cli_step_ok("✔", 'Analisando', 'docker-compose.yaml');
        (new \Dpp\BuildDockerCompose(null))
            ->setProjectDir($this->projectDir)
            ->save(dirname($this->projectDir));

        // TODO
        // Verificar se o container já existe e perguntar se usuario quer matá-lo
        // ...
        
        // $this->removeFiles();

        return $this;
    }

    private function makeDir()
    {
        $parent = dirname($this->projectDir);
        if (\is_writable($parent) == false) {
            cli_error("Não há permissão para escrever no diretório $parent", true);
        }

        if (is_dir($this->projectDir) == false) {
            mkdir($this->projectDir, 0777, true);
            chmod($this->projectDir, 0777);
        }
        
    }

    // private function removeFiles()
    // {
    //     shell_exec("rm -Rf {$this->projectDir}");
    //     // \rmdir($this->projectDir);

    //     $parentDir = dirname($this->projectDir);
    //     unlink($parentDir . DIRECTORY_SEPARATOR . 'docker-compose.yaml');
    // }

}