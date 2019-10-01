<?php
namespace Dpp\NGINX;

class BuildDockerFile extends \Dpp\BuildDockerFile
{
    protected function getFilename()
    {
        return 'DockerfileNGINX';
    }

    protected function handle()
    {
        $versionString = $this->getVersionString();
        $this->add("FROM nginx:{$versionString}");
        $this->copyFiles($this->projectDir);
    }

    protected function copyFiles($destiny)
    {
        $conf = __DIR__ . DIRECTORY_SEPARATOR . 'nginx-noredir.conf';
        $docroot = '';

        $redir = $this->getParam('redir-index');
        if ($redir != null && (bool) $redir !== false) {

            $docroot = $this->getParam('redir-target', '');
            $conf    = __DIR__ . DIRECTORY_SEPARATOR . 'nginx-redir.conf';
            if($docroot != '' && strpos($docroot, '/') !== false) {
                $docroot = path_dirname($docroot);
            }
        }

        $workdir     = $this->getParam('workdir', '/var/www/html');
        $contents    = path_get_contents($conf);
        $projectPath = rtrim($workdir, '/') . '/' . $docroot;
        $contents    = str_replace('{{ project_path }}', $projectPath, $contents);

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