<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/utils.php';
require __DIR__ . '/../src/models.php';

$app = AppFactory::create();

$storage = new frostealth\storage\ArrayData();

//Open the Home Page of the app
$app->get('/', function (Request $request, Response $response, $args) use ($storage) {
    $response->getBody()->write("Welcome to TODO App!");
    return $response;
});

//Create a TODO with a name of your choice
$app->get('/create/{name}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $temp = getTodoModel();
    $temp['id'] = rand(100000,200000);
    $temp['name'] = $args['name'];
    if(is_null($data)){
        $data[] = $temp;
    } else {
        array_push($data, $temp);
    }
    putJson($data);
    $response->getBody()->write("Todo Added: " . json_encode($temp));
    return $response->withHeader('Content-Type', 'application/json');
});

//Update the Name of the TODO
$app->get('/update-name/{oldname}/{newname}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    foreach($data as $key => $value){
        if($value['name'] == $args['oldname']){
            $data[$key]['name'] = $args['newname']; 
            break;
        }
    }
    putJson($data);
    $response->getBody()->write("Todo Name Updated: " . "from -> " . $args['oldname'] . " to -> " .  $args['newname']);
    return $response->withHeader('Content-Type', 'application/json');
});

//Update a particular Item in a TODO of your choice
$app->get('/update-item/{todoname}/{itemname}/{newname}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $found = false;
    foreach($data as $key => $value){
        if($value['name'] == $args['todoname']){
            if($args['itemname'] == 'is_complete'){
                $data[$key]['is_complete'] = $args['newname']; 
            } else {
                $data[$key][$args['itemname']] = $args['newname']; 
            }
            $found = true;
            break;
        }
    }
    
    if(!$found){
        $response->getBody()->write("Could Not Find Todo!");
    } else {
        putJson($data);
        $response->getBody()->write("Todo Item Updated: " . "from -> " . $args['itemname'] . " to -> " .  $args['newname']);
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});

//Add a new item to a TODO of your choice
$app->get('/add-item/{todoname}/{itemname}/{itemvalue}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $found = false;
    foreach($data as $key => $value){
        if($value['name'] == $args['todoname']){
            $data[$key][$args['itemname']] = $args['itemvalue']; 
            $found = true;
            break;
        }
    }
    if(!$found){
        $response->getBody()->write("Could Not Find Todo!");
    } else {
        putJson($data);
        $response->getBody()->write("Todo Item Added: " . "for todo -> " . $args['todoname'] . " An item -> " . $args['itemname'] . " with value -> " .  $args['itemvalue']);
    }
    return $response->withHeader('Content-Type', 'application/json');
});


/* Incomplete Functionality */
$app->get('/delete-todo-item/{todoname}/{itemname}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $found = false;
    foreach($data as $key => $value){
        if($value['name'] == $args['todoname']){
            foreach($value as $i => $item){ 
                if($i == $args['itemname']){
                    array_splice($data[$key], $i, 1);
                    $found = true;
                }
            }
        }
    }
    if(!$found){
        $response->getBody()->write("Could Not Find Todo!");
    } else {
        putJson($data);
        $response->getBody()->write("Deleted Item: " . $args['itemname']);
    }
    return $response->withHeader('Content-Type', 'application/json');
});

// Delete an existing TODO
$app->get('/delete-todo/{name}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $found = false;
    foreach($data as $key => $value){
        if($value['name'] == $args['name']){
            array_splice($data, $key, 1);
            $found = true;
        }
    }
    if(!$found){
        $response->getBody()->write("Could Not Find Todo!");
    } else {
        putJson($data);
        $response->getBody()->write("Deleted: " . $args['name']);
    }
    return $response->withHeader('Content-Type', 'application/json');
});

// Retrieve a specific TODO
$app->get('/retrieve/{name}', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $found = false;
    foreach($data as $key => $value){
        if($value['name'] == $args['name']){
            $response->getBody()->write(json_encode($value));
            $found = true;
            break;
        }
    }
    if(!$found){
        $response->getBody()->write("Could Not Find Todo!");
    }
    return $response->withHeader('Content-Type', 'application/json');
});

//Retrieve All TODOs
$app->get('/retrieve-todos', function (Request $request, Response $response, $args) use ($storage) {
    $data = getTodoList();
    $response->getBody()->write(json_encode(getTodoList()));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();