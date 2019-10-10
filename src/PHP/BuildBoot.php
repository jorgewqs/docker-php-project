<?php
namespace Dpp\PHP;

use Dpp\Register;

class BuildBoot extends \Dpp\Build
{
    private $workDir;

    protected function getFilename()
    {
        return 'boot.sh';
    }
    
    protected function handle()
    {
        // Diretório onde o script está sendo executado
        $this->workDir = getcwd();

        $this->add('#!/bin/bash');
        $this->add(' ');

        $workdir = Register::getInstance()->getDefaultParam('workdir');
        $this->add(' ');
        $this->add("cd '{$workdir}';");

        $gitName = Register::getInstance()->getDefaultParam('git-commiter-name');
        $gitEmail = Register::getInstance()->getDefaultParam('git-commiter-email');

        if ($gitName != null || $gitEmail != null) {
            $this->add(' ');
            $this->add("# Setagem do usuário comitador do git");
        }
        if ($gitName != null) {
            $this->add("git config user.name '{$gitName}';");
        }
        if ($gitEmail != null) {
            $this->add("git config user.email '{$gitEmail}';");
        }
        
        $this->add(' ');
        $this->add("# As informações do dono do projeto fora do docker");
        $userID    = str_replace("\n", "", command_exec("stat -c '%u' {$this->workDir}"));
        $userName  = str_replace("\n", "", command_exec("stat -c '%U' {$this->workDir}"));
        $groupID   = str_replace("\n", "", command_exec("stat -c '%g' {$this->workDir}"));
        $groupName = str_replace("\n", "", command_exec("stat -c '%G' {$this->workDir}"));
        $this->add("REAL_USER_ID={$userID};");
        $this->add("REAL_USER_NAME={$userName};");
        $this->add("REAL_GROUP_ID={$groupID};");
        $this->add("REAL_GROUP_NAME={$groupName};");

        $this->add(' ');
        $this->add("# As informações do dono do projeto dentro do docker");
        $this->add("CURR_USER_ID=$(stat -c '%u' .);");
        $this->add("CURR_USER_NAME=$(stat -c '%U' .);");
        $this->add("CURR_GROUP_ID=$(stat -c '%g' .);");
        $this->add("CURR_GROUP_NAME=$(stat -c '%G' .);");

        $this->add(' ');
        $this->add("# Cria o usuário local dentro do conteiner se este não existir");

        // Grupo
        $this->add('if [ "$REAL_GROUP_NAME" != "$CURR_GROUP_NAME" ] && [ -z "$(grep $REAL_GROUP_NAME /etc/group)" ]; then');
        $icon = cli_color('blue', "→");
        $message = " Criando grupo " . cli_color('blue', '$REAL_GROUP_NAME ($REAL_GROUP_ID)');
        $this->add("echo -e \"{$icon} {$message}\";");
        $this->add('addgroup --quiet --gid "$REAL_GROUP_ID" "$REAL_GROUP_NAME"', 1);
        $this->add('fi');

        // usuario
        $this->add('if [ "$REAL_USER_NAME" != "$CURR_USER_NAME" ] && [ -z "$(grep $REAL_USER_NAME /etc/passwd)" ]; then');
        $icon = cli_color('blue', "→");
        $message = " Criando usuário " . cli_color('blue', '$REAL_USER_NAME ($REAL_USER_ID)');
        $this->add("echo -e \"{$icon} {$message}\";");
        $this->add('useradd --uid "$REAL_GROUP_ID" --gid "$REAL_GROUP_ID" --create-home --comment "$REAL_USER_NAME" "$REAL_USER_NAME"', 1);
        $this->add('cat /root/.bashrc > /home/$REAL_USER_NAME/.bashrc', 1);
        $this->add('fi');

        $icon = cli_color('green', "✔");
        $this->add("echo -e \"{$icon} Projeto executando\";");
    }
}