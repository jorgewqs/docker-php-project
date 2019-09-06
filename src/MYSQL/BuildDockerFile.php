<?php
namespace Dpp\MYSQL;

class BuildDockerFile extends \Dpp\BuildDockerFile
{
    protected function handle()
    {
        $versionString = $this->getVersionString();
        $projectDir = basename($this->getProjectDir());
        
        $this->add("FROM mysql:{$versionString}");

        $workdir = $this->getParam('workdir');
        if ($workdir != null) {
            // $this->add("WORKDIR \"{$workdir}\"");
            // $this->add("VOLUME [\"{$workdir}\"]");
        }

        $this->copyFiles($this->projectDir);
        //$this->add("COPY {$projectDir}/mysql.cnf /etc/mysql/my.cnf");
    }

    protected function copyFiles($destiny)
    {
        $conf = __DIR__ . DIRECTORY_SEPARATOR . 'mysql.cnf';
        copy($conf, $destiny . DIRECTORY_SEPARATOR . 'mysql.cnf');
    }

    protected function getVersionString()
    {
        $version = $this->getVersion();

        switch($version){
            case 80: 
                $prefix = '8.0'; 
                break;
            case 57: 
                $prefix = '5.7'; 
                break;
            case 56: 
                $prefix = '5.7'; 
                break;
            case 55: 
                $prefix = '5.5'; 
                break;
        }

        return $prefix;
    }
}