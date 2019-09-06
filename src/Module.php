<?php
namespace Dpp;

class Module
{
    private $module = null;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function version($string)
    {
        Register::getInstance()
            ->addVersion($this->module, $string);
        return $this;
    }

    public function param($name, $value)
    {
        Register::getInstance()
            ->addParam($this->module, $name, $value);
        return $this;
    }

    public function extension($name)
    {
        Register::getInstance()
            ->addExtension($this->module, $name);
        return $this;
    }

    public function tool($name)
    {
        Register::getInstance()
            ->addTool($this->module, $name);
        return $this;
    }
}