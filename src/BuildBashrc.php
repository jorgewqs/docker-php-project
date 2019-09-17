<?php
namespace Dpp;

class BuildBashrc extends Build
{
    protected function handle()
    {
        $this->add('version: "3.1"');
        $this->add('services:');
        $this->add(' ');

        Register::getInstance()->all();
        path_basename($this->getProjectDir());
    }
}