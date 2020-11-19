<?php

function getTodoList()
{   
    if(!file_exists(__DIR__ . '/todo.json')){
        $myfile = fopen(__DIR__ . "/todo.json", "w") or die("Unable to open file!");
    }
    return json_decode(file_get_contents(__DIR__ . '/todo.json'), true);
}

function putJson($data)
{
    file_put_contents(__DIR__ . '/todo.json', json_encode($data, JSON_PRETTY_PRINT));
}

?>