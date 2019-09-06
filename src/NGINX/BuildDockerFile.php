<?php
namespace Dpp\NGINX;

class BuildDockerFile extends \Dpp\BuildDockerFile
{
    protected function handle()
    {
        $versionString = $this->getVersionString();
        $projectDir = basename($this->getProjectDir());
        
        $this->add("FROM nginx:{$versionString}");

        $workdir = $this->getParam('workdir');
        if ($workdir != null) {
            // $this->add("WORKDIR \"{$workdir}\"");
            // $this->add("VOLUME [\"{$workdir}\"]");
        }

        $this->copyFiles($this->projectDir);
        //$this->add("COPY {$projectDir}/nginx.conf /etc/nginx/conf.d/default.conf");
    }

    protected function copyFiles($destiny)
    {
        $redir = $this->getParam('redir-index');
        if ($redir != null && (bool) $redir !== false) {
            $conf = __DIR__ . DIRECTORY_SEPARATOR . 'nginx-redir.conf';
        } else {
            $conf = __DIR__ . DIRECTORY_SEPARATOR . 'nginx-noredir.conf';
        }

        $workdir = $this->getParam('workdir');

        $contents = file_get_contents($conf);
        $contents = str_replace('{{ project_path }}', $workdir, $contents);

        file_put_contents($destiny . DIRECTORY_SEPARATOR . 'nginx.conf', $contents);
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