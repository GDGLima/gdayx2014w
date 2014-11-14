<?php

$di = new \Phalcon\DI\FactoryDefault();

//Set up the database service
$di->set('db', function(){
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => "localhost",
        "username" => "root",
        "password" => "rootbiomedical",
        "dbname" => "robotics"
    ));
});

$app = new \Phalcon\Mvc\Micro();

//Bind the DI to the application
$app->setDI($di);

//------

$app = new Phalcon\Mvc\Micro();

//Retrieves all robots
$app->get('/api/robots', function() {

});

//Searches for robots with $name in their name
$app->get('/api/robots/search/{name}', function($name) {

});

//Retrieves robots based on primary key
$app->get('/api/robots/{id:[0-9]+}', function($id) {

});

//Adds a new robot
$app->post('/api/robots', function() {

});

//Updates robots based on primary key
$app->put('/api/robots/{id:[0-9]+}', function() {

});

//Deletes robots based on primary key
$app->delete('/api/robots/{id:[0-9]+}', function() {

});

$app->handle();

?>