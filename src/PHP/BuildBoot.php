<?php
namespace Dpp\PHP;

use Dpp\Register;

class BuildBoot extends \Dpp\Build
{
    protected function getFilename()
    {
        return 'boot.sh';
    }
    
    protected function handle()
    {
        $this->add('#!/bin/bash');
        $this->add(' ');

        $workdir = Register::getInstance()->getDefaultParam('workdir');
        $this->add("cd '{$workdir}';");

        $gitName = Register::getInstance()->getDefaultParam('git-commiter-name');
        if ($gitName != null) {
            $this->add("git config user.name '{$gitName}';");
        }

        $gitEmail = Register::getInstance()->getDefaultParam('git-commiter-email');
        if ($gitEmail != null) {
            $this->add("git config user.email '{$gitEmail}';");
        }
        
        //$projectDir = path_basename($this->getProjectDir());
    }
}