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

        $containers = Register::getInstance()->all();

        foreach($containers as $name => $config) {

            cli_info("Gerando arquivos do " . strtoupper($name) . PHP_EOL);

            $className = "\\Dpp\\" . strtoupper($name) . '\\BuildDockerFile';
            if ( class_exists($className) ) {
                (new $className($config))
                    ->setProjectDir($this->projectDir)
                    ->save($this->projectDir);
            }
        }

        cli_info("Gerando docker-compose.yaml" . PHP_EOL);
        (new \Dpp\BuildDockerCompose(null))
            ->setProjectDir($this->projectDir)
            ->save(dirname($this->projectDir));

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