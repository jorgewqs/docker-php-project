<?php 

// Este script gera um arquivo Dockerfile de acordo com as especificações
// de configuração

/**
 * Libera uma mensagem para o terminal
 * @param string $message
 */
function cli_out($message)
{
    fwrite(STDOUT, "$message\n");
}

function cli_exec($command)
{
    // Fica observando saídas bem sucedidas
    // https://www.php.net/manual/pt_BR/function.ob-end-flush.php
    while (@ob_end_flush()); 

    // Abre o observador do processo
    $proc = popen("$command 2>&1; echo Status:$?", 'r');
    $live_output     = "";
    $status          = "";
    $complete_output = "";

    // Enquanto existem saidas, elas são liberadas
    while(!feof($proc)) {

        $live_output = trim(fread($proc, 4096));

        if (preg_match("/^Status:.*$/", $live_output)) {
            $status = str_replace('Status:', '', $live_output);
        } elseif(empty($live_output) === false){
            $complete_output = $complete_output . $live_output . "\n";
            cli_out("$live_output");
        }
       
        @flush();
    }

    // Fecha o observador do processo
    pclose($proc);

    // retorna o status da saída e a conteúdo completo exibido na tela
    return array (
       'exit_status' => $status,
       'output'      => trim($complete_output)
    );
}

/**
 * Gera uma string colorida para exibir no terminal
 * @param array $color red|green|yellow|blue
 * @param string $message
 * @return string
 */
function cli_color($color, $message)
{
    $string = '';

    switch($color) {
        case 'yellow';
            $string .= "\e[33m";
            break;

        case 'red';
            $string .= "\033[0;31m";
            break;

        case 'green';
            $string .= "\033[0;32m";
            break;

        case 'blue';
            $string .= "\e[34m";
            break;
    }

    $string .= $message;

    // cor normal
    $string .= "\033[0m";

    return $string;
}

/**
 * Exibe uma mensagem para o terminal
 * @param array $type error|success|warning
 * @param string $message
 * @return void
 */
function cli_message($type, $message)
{
    $string = '';

    switch($type) {
        case 'success';
            $string .= cli_color('green', $message);
            break;

        case 'error';
            $string .= cli_color('red', $message);
            break;

        case 'warning';
            $string .= cli_color('yellow', $message);
            break;

        case 'info';
            $string .= cli_color('blue', $message);
            break;

        default:
            $string .= $message;
    }

    cli_out("$string");
}

/**
 * Exibe uma mensagem com sinal de conclusão para o terminal
 * @param array $type error|success|warning
 * @param string $message
 * @param string $append rexto após o status
 * @return void
 */
function cli_step($type, $message, $append = '')
{
    $string = '';

    switch($type) {
        case 'success';
            $string .= $message . ": " . cli_color('green', 'OK') . " $append";
            break;

        case 'error';
            $string .= $message . ": " . cli_color('red', 'ERRO') . " $append";
            break;

        case 'warning';
            $string .= $message . ": " . cli_color('red', 'ATENÇÃO') . " $append";
            break;

        case 'skip';
            $string .= $message . ": " . cli_color('blue', 'PULAR') . " $append";
            break;

        default:
            $string .= $message  . " $append";
    }

    cli_out("$string");
}

