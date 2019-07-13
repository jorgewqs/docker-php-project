<?php 

/*
 * Autor: Ricardo Pereira <contato@ricardopdias.com.br>
 * Site: https://www.ricardopdias.com.br
 * 
 * Este arquivo inclui as funções para geração de 
 * conteúdo para os arquivos
 */

function append_contents($tag, $string)
{
    global $filecontents;

    if($filecontents == null) {
        $filecontents = [];
    }

    if (! isset($filecontents[$tag])) {
        $filecontents[$tag] = "";    
    }

    $filecontents[$tag] .= $string . "\n";
}

function get_contents($tag)
{
    global $filecontents;

    if($filecontents != null && isset($filecontents[$tag])) {
        return $filecontents[$tag];
    }

    return "";
}