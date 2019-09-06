<?php
namespace Dpp;

class Register
{
    static $instance = null;

    private $params = [];

    public static function getInstance() : Register
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function init($module)
    {
        if (! isset($this->params[$module])) {
            $this->params[$module] = [
                'version'    => 0,
                'params'     => [],
                'extensions' => [],
                'tools'      => []
            ];
        }
    }

    // Versão

    public function addVersion($module, $version)
    {
        $this->init($module);
        $this->params[$module]['version'] = (int) preg_replace('#[^0-9]#', '', $version);
        return $this;
    }

    public function getVersion($module)
    {
        $this->init($module);
        return $this->params[$module]['version'] ?? null;
    }

    // Parâmetros

    public function addParam($module, $param, $value)
    {
        $this->init($module);
        $this->params[$module]['params'][$param] = $value;
        return $this;
    }

    public function getParam($module, $param)
    {
        $this->init($module);
        return $this->params[$module]['params'][$param] ?? null;
    }

    public function removeParam($module, $param)
    {
        $this->init($module);
        if (isset($this->params[$module]['params'][$param])) {
            usnet($this->params[$module]['params'][$param]);
        }
        return $this;
    }

    // Extensões

    public function addExtension($module, $value)
    {
        $this->init($module);
        $this->params[$module]['extensions'][] = $value;
        return $this;
    }

    protected function hasExtension($module, $name)
    {
        $extensions = array_flip($this->params[$module]['extensions']);
        return isset($extensions[$name]);
    }

    public function removeExtension($module, $name)
    {
        $this->init($module);

        foreach($this->params[$module]['extensions'] as $index => $label) {
            if($label == $name) {
                unset($this->params[$module]['extensions'][$index]);
                break;
            }
        }
        
        return $this;
    }

    // Ferramentas

    public function addTool($module, $value)
    {
        $this->init($module);
        $this->params[$module]['tools'][] = $value;
        return $this;
    }

    protected function hasTool($module, $name)
    {
        $extensions = array_flip($this->params[$module]['tools']);
        return isset($extensions[$name]);
    }

    public function removeTool($module, $name)
    {
        $this->init($module);

        foreach($this->params[$module]['tools'] as $index => $label) {
            if($label == $name) {
                unset($this->params[$module]['tools'][$index]);
                break;
            }
        }
        
        return $this;
    }

    // Defaults Parâmetros

    public function addDefaultParam($param, $value)
    {
        return $this->addParam('defaults', $param, $value);
    }

    public function getDefaultParam($param)
    {
        return $this->getParam('defaults', $param);
    }

    public function removeDefaultParam($param)
    {
        return $this->removeParam('defaults', $param);
    }

    public function all()
    {
        $params = $this->params;
        unset($params['defaults']);
        return $params;
    }

    public function defaults()
    {
        return $this->params['defaults'] ?? [];
    }
    
}