<?php
namespace Dpp;

abstract class BuildDockerFile extends \Dpp\Build
{
    public function getParam($name, $default = null)
    {
        $param = $this->getDefaultParam($name);

        if (parent::hasParam($name)) {
            $param = parent::getParam($name);
        }

        return $param ?? $default;
    }

    protected function addSeparator($name)
    {
        $this->add('');
        $this->add('# ' . $name);
        $this->add('');
    }

    protected function insertTools()
    {
        $tools = $this->getTools();

        // $this->add('RUN apt-get update;');  

        foreach($tools as $name) {

            switch($name) {
                case 'composer':
                    $this->add('RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer;');  
                    $this->add('RUN composer global require hirak/prestissimo;');  
                    $this->add('RUN composer clear-cache;');  
                    $this->add('RUN rm -rf ~/.composer;');
                    break;
                case 'git':
                    $this->add('RUN apt-get -y install git;');  
                    break;
            }
        }

    }
    
}