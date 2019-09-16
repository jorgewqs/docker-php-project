<?php
namespace Dpp;

class Task
{
    private $name = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function run($command)
    {
        Register::getInstance()
            ->addTask($this->name, $command);
        return $this;
    }
}