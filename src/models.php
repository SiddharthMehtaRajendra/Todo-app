<?php

$todo = [
    'id' => '',
    'name' => '',
    'task' => '',
    'is_complete' => '',
    'owner' => '',
];

function getTodoModel(){
    global $todo;
    return $todo;
}

?>
