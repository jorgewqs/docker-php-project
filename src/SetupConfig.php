<?php
namespace Dpp;

class SetupConfig
{
    private $userConfigDir = null;

    public function __construct()
    {
        $this->userConfigDir = str_replace("\n", "", command_exec('echo $HOME')) 
                          . DIRECTORY_SEPARATOR 
                          . '.docker-php-project';
    }

    public function run()
    {
        $this->makeConfigDir();

        if ($this->hasConfigurationChanges() == true) {
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

        $configHash = $this->userConfigDir . DIRECTORY_SEPARATOR . 'config-hash';
        if (! has_file($configHash)) {
            $hash = $this->getConfigurationHash();
            path_put_contents($configHash, $hash);
        }
    }

    function hasConfigurationChanges()
    {
        $lastHash = $this->getLastConfigurationHash();
        $currentHash = $this->getConfigurationHash();
        
        if ($lastHash != $currentHash) {
            $configHash = $this->userConfigDir . DIRECTORY_SEPARATOR . 'config-hash';
            path_put_contents($configHash, $currentHash);
            return true;
        }
        
        return false;
    }

    private function getConfigurationHash()
    {
        $contents = path_get_contents(getcwd() . '/docker.php');
        return md5($contents);
    }

    function getLastConfigurationHash()
    {
        $configHash = $this->userConfigDir . DIRECTORY_SEPARATOR . 'config-hash';
        return (has_file($configHash))
            ? path_get_contents($configHash)
            : null;
    }



}