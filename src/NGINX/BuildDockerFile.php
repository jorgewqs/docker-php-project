<?php
namespace Dpp\NGINX;

class BuildDockerFile extends \Dpp\BuildDockerFile
{
    protected function handle()
    {
        $versionString = $this->getVersionString();
        $this->add("FROM nginx:{$versionString}");
        $this->copyFiles($this->projectDir);
    }

    protected function copyFiles($destiny)
    {
        $conf = __DIR__ . DIRECTORY_SEPARATOR . 'nginx-noredir.conf';

        $redir = $this->getParam('redir-index');
        if ($redir != null && (bool) $redir !== false) {
            $conf = __DIR__ . DIRECTORY_SEPARATOR . 'nginx-redir.conf';
        }

        $workdir = $this->getParam('workdir', '/var/www/html');
        $contents = path_get_contents($conf);
        $contents = str_replace('{{ project_path }}', $workdir, $contents);

        path_put_contents($destiny . DIRECTORY_SEPARATOR . 'nginx.conf', $contents);
    }

    protected function getVersionString()
    {
        $version = $this->getVersion();
        
        switch($version){
            case 117: 
                $prefix = '1.17'; 
                break;
            case 116: 
                $prefix = '1.16'; 
                break;
            case 115: 
                $prefix = '1.15'; 
                break;
        }

        return $prefix;
    }
}