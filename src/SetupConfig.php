<?php
namespace Dpp;

class SetupConfig
{
    private $workDir = null;

    private $userConfigDir = null;

    public function __construct()
    {
        // Diretório onde o script está sendo executado
        $this->workDir = getcwd();

        $this->userConfigDir = str_replace("\n", "", command_exec('echo $HOME')) 
                          . DIRECTORY_SEPARATOR 
                          . '.docker-php-project';
    }

    public function run()
    {
        $this->makeConfigDir();

        if ($this->hasConfigChanges() == true) {
            cli_step_info('Detectada nova configuração', 'Forçando rebuild');
        } else {
            echo 'no';
        }
    }

    private function makeConfigDir()
    {
        if (! has_dir($this->userConfigDir)) {
            make_dir($this->userConfigDir, 0777, true);
        }

        $configFile = $this->getConfigHashFile();
        if (! has_file($configFile)) {
            path_put_contents($configFile, $this->getConfigHash());
        }
    }

    private function getConfigHashFile()
    {
        return $this->userConfigDir . DIRECTORY_SEPARATOR . 'project-' . md5($this->workDir);
    }

    function hasConfigChanges()
    {
        $lastHash = $this->getLastConfigHash();
        $currentHash = $this->getConfigHash();
        
        if ($lastHash != $currentHash) {
            $configFile = $this->getConfigHashFile();
            path_put_contents($configFile, $currentHash);
            return true;
        }
        
        return false;
    }

    private function getConfigHash()
    {
        $contents = path_get_contents($this->workDir . '/docker.php');
        return md5($contents);
    }

    function getLastConfigHash()
    {
        $configFile = $this->getConfigHashFile();
        return (has_file($configFile))
            ? path_get_contents($configFile)
            : null;
    }
}