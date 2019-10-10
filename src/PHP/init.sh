#!/bin/bash

#
# Autor: Ricardo Pereira <contato@ricardopdias.com.br>
# Site: https://www.ricardopdias.com.br
# 
# Este programa faz as configurações necessárias para compatibilidade 
# e personalização do projeto dentro do docker
#
# 

echo 'XXX';

# $CURRENT_DIR=$PWD
# cd /app; git config user.name 'XXX'
# cd /app; git config user.email 'xxx@xxx.com.br'
# cd $CURRENT_DIR

# https://wime.com.br/2013/06/06/como-adicionar-e-excluir-usuarios-no-ubuntu-12-04-e-centos-6/
        # // $workdir = Register::getInstance()->getParam('defaults', 'workdir');
        # // $commiterName = Register::getInstance()->getParam('bash', 'commiter-name');
        # // if ($commiterName !== null) {
        # //     $this->add("RUN cd {$workdir}; git config user.name '{$commiterName}';");
        # // }

        # // $commiterEmail = Register::getInstance()->getParam('bash', 'commiter-email');
        # // if ($commiterName !== null) {
        # //     $this->add("RUN cd {$workdir}; git config user.email '{$commiterEmail}';");
        # // }

        # // $directoryUser = Register::getInstance()->getParam('bash', 'commiter-email');
        # // if ($commiterName !== null) {
        # //     $this->add("RUN cd {$workdir}; git config user.email '{$commiterEmail}';");
        # // }
        

        # //     Register::getInstance()
        # //         ->addTask('setup', "chgrp {$value} {$this->sourceDir}")
        # //         ->addTask('setup', "chmod g+s {$value}")
        # //         ->addTask('setup', "find {$this->sourceDir} -type d -exec chgrp {$value} {}")
        # //         ->addTask('setup', "find {$this->sourceDir} -type d -exec chmod g+s {}");
        # // }

        # // if ($name === 'user') {
        # //     Register::getInstance()
        # //         ->addTask('setup', "chown {$value} {$this->sourceDir}")
        # //         ->addTask('setup', "chmod g+s {$value}")
        # //         ->addTask('setup', "find {$this->sourceDir} -type d -exec chmod g+s {}");
        # // }

        # // adduser renan -home /home/renan

        # // addgroup usuariosftp

        # // usermod -a -G usuariosftp renan
