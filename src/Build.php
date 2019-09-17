<?php
namespace Dpp;

abstract class Build
{
    protected $projectDir;
    protected $data = [];

    private $moduleConfig;

    public function __construct($moduleConfig = null)
    {
        $this->moduleConfig = $moduleConfig;
    }

    abstract protected function getFilename();

    abstract protected function handle();

    public function setProjectDir($path)
    {
        $this->projectDir = $path;
        return $this;
    }

    public function getProjectDir()
    {
        return $this->projectDir;
    }

    protected function add(string $string, $level = 0)
    {
        $prefix = str_repeat(" ", $level * 4);
        $this->data[] = $prefix . $string;
        return $this;
    }

    protected function render()
    {
        $this->handle();
        return implode(PHP_EOL, $this->data); 
    }

    public function save($distiny)
    {
        $filename = $distiny . DIRECTORY_SEPARATOR . $this->getFilename();
        $contents = $this->render();
        path_put_contents($filename, $contents);
    }

    // Consultas

    public function getVersion()
    {
        return $this->moduleConfig['version'];
    }

    public function hasModule($name)
    {
        $globalConfig = Register::getInstance()->all();
        return isset($globalConfig[$name]);
    }

    public function hasParam($name)
    {
        return isset($this->moduleConfig['params'][$name]);
    }

    public function getParam($name)
    {
        return $this->moduleConfig['params'][$name] ?? null;
    }

    public function removeParam($name)
    {
        if ($this->hasModuleParam($name)) {
            unset($this->moduleConfig['params'][$name]);
        }
    }

    public function hasExtension($name)
    {
        $extensions = array_flip($this->moduleConfig['extensions']);
        return isset($extensions[$name]);
    }

    public function getExtensions()
    {
        return $this->moduleConfig['extensions'];
    }

    public function removeExtension($name)
    {
        foreach($this->moduleConfig['extensions'] as $index => $label) {
            if($label == $name) {
                unset($this->moduleConfig['extensions'][$index]);
                break;
            }
        }
    }

    public function hasTool($name)
    {
        $tools = array_flip($this->moduleConfig['tools']);
        return isset($tools[$name]);
    }

    public function getTools()
    {
        return $this->moduleConfig['tools'];
    }

    public function removeTool($name)
    {
        foreach($this->moduleConfig['tools'] as $index => $label) {
            if($label == $name) {
                unset($this->moduleConfig['tools'][$index]);
                break;
            }
        }
    }


    public function hasDefaultParam($name)
    {
        return Register::getInstance()->hasDefaultParam($name);
    }

    public function getDefaultParam($name)
    {
        return Register::getInstance()->getDefaultParam($name);
    }

    public function removeDefaultParam($name)
    {
        return Register::getInstance()->removeDefaultParam($name);
    }

    // Mensagens

    public function error($message)
    {
        cli_error($message, true);
    }

    public function warn($message)
    {
        cli_warn($message);
    }

    public function success($message)
    {
        cli_ok($message);
    }
    
}