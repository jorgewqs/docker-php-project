<?php
namespace Dpp;

class ProjectTasks
{
    private $projectDir = null;

    public function __construct()
    {
        $this->projectDir = getcwd() // diretório onde o script está sendo executado
            . '/.docker-project'; // diretóio temporário
    }

    public function run()
    {
        // Conteiners executando
        cli_step_ok('Conteiners ativos', '');

        $tasks = Register::getInstance()->getTasks();
        $stop = false;
        foreach($tasks as $name => $steps) {

            if ($stop == true) {
                break;
            }

            cli_step_run('Executando tarefa', $name);

            $topDir = getcwd();
            cli_step_info('Diretório atual', $topDir);
            
            foreach($steps as $command) {

                $command = trim($command);
                $command = trim($command, ";");

                if (preg_match('#^cd.*#', $command)) {
                    $command = trim(str_replace('cd', '', $command));
                    change_dir($command);
                    if (preg_match("#{$topDir}#", getcwd())) {
                        cli_step_info('Diretório atual', getcwd());
                        continue;
                    }

                    cli_step_error('Não é permitido acessar', "\"".getcwd()."\"");
                    cli_step_error('O escopo do projeto está em', "\"{$topDir}\"");
                    cli_error('Tarefas encerradas!');
                    break;
                }

                $newcommand = "exec 2>&1; " // exec 2>&1 direciona a saída de STDERR para STDOUT 
                         . $command 
                         . "; echo \"---OK---\""; // para saber que todo o script foi executado

                $output = command_exec($newcommand);
                $executed = (bool) preg_match('#---OK---#', $output);
                $hasError = (bool) preg_match('#sh\:#', $output);

                $outputCleaned = trim(str_replace("---OK---", "", $output), "\n");

                if ($executed == true && $hasError == false) {
                    if (preg_match('#^echo.*#', $command)) {
                        cli_step_echo('"' . $outputCleaned . '"', '');
                    }
                    continue;
                } 

                if ($executed == false) {
                    cli_step_error('O comando não executou completamente', "\"$command\"");
                    cli_out($outputCleaned . PHP_EOL);
                    $stop = true;
                    break;
                } 
                
                if ($hasError == true) {
                    cli_step_error('Um erro aconteceu ao executar', "\"$command\"");
                    cli_out($outputCleaned . PHP_EOL);
                    $stop = true;
                    break;
                }
            }
        }
    }
}