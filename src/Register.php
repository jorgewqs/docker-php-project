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

    private function init($tag)
    {
        if (! isset($this->params[$tag])) {
            $this->params[$tag] = [
                'version'    => 0,
                'params'     => [],
                'commands'   => [],
                'extensions' => [],
                'tools'      => []
            ];
        }
    }

    // Versão

    public function addVersion($tag, $version)
    {
        $this->init($tag);
        $this->params[$tag]['version'] = (int) preg_replace('#[^0-9]#', '', $version);
        return $this;
    }

    public function getVersion($tag)
    {
        $this->init($tag);
        return $this->params[$tag]['version'] ?? null;
    }

    // Parâmetros

    public function addParam($tag, $param, $value)
    {
        $this->init($tag);
        $this->params[$tag]['params'][$param] = $value;
        return $this;
    }

    public function getParam($tag, $param)
    {
        $this->init($tag);
        return $this->params[$tag]['params'][$param] ?? null;
    }

    public function removeParam($tag, $param)
    {
        $this->init($tag);
        if (isset($this->params[$tag]['params'][$param])) {
            usnet($this->params[$tag]['params'][$param]);
        }
        return $this;
    }

    // Tarefas

    public function addTask($name, $value)
    {
        $this->init('tasks');
        if (!isset($this->params['tasks']['commands'][$name])) {
            $this->params['tasks']['commands'][$name] = [];
        }
        $this->params['tasks']['commands'][$name][] = $value;
        return $this;
    }

    public function getTask($name)
    {
        $this->init('tasks');
        return $this->params['tasks']['commands'][$name] ?? null;
    }

    public function removeTask($name)
    {
        $this->init('tasks');
        if (isset($this->params['tasks']['commands'][$name])) {
            usnet($this->params['tasks']['commands'][$name]);
        }
        return $this;
    }

    public function getTasks()
    {
        $this->init('tasks');
        return $this->params['tasks']['commands'];
    }

    // Extensões

    public function addExtension($tag, $value)
    {
        $this->init($tag);
        $this->params[$tag]['extensions'][] = $value;
        return $this;
    }

    protected function hasExtension($tag, $name)
    {
        $extensions = array_flip($this->params[$tag]['extensions']);
        return isset($extensions[$name]);
    }

    public function removeExtension($tag, $name)
    {
        $this->init($tag);

        foreach($this->params[$tag]['extensions'] as $index => $label) {
            if($label == $name) {
                unset($this->params[$tag]['extensions'][$index]);
                break;
            }
        }
        
        return $this;
    }

    // Ferramentas

    public function addTool($tag, $value)
    {
        $this->init($tag);
        $this->params[$tag]['tools'][] = $value;
        return $this;
    }

    protected function hasTool($tag, $name)
    {
        $extensions = array_flip($this->params[$tag]['tools']);
        return isset($extensions[$name]);
    }

    public function removeTool($tag, $name)
    {
        $this->init($tag);

        foreach($this->params[$tag]['tools'] as $index => $label) {
            if($label == $name) {
                unset($this->params[$tag]['tools'][$index]);
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