<?php

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Robots extends Model
{

    public function validation()
    {
        //Type must be: droid, mechanical or virtual
        $this->validate(new InclusionIn(
            array(
                "field"  => "type",
                "domain" => array("droid", "mechanical", "virtual")
            )
        ));

        //Robot name must be unique
        $this->validate(new Uniqueness(
            array(
                "field"   => "name",
                "message" => "The robot name must be unique"
            )
        ));

        //Year cannot be less than zero
        if ($this->year < 0) {
            $this->appendMessage(new Message("The year cannot be less than zero"));
        }

        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}

//Retrieves all robots
$app->get('/api/robots', function() use ($app) {

    $phql = "SELECT * FROM Robots ORDER BY name";
    $robots = $app->modelsManager->executeQuery($phql);

    $data = array();
    foreach ($robots as $robot) {
        $data[] = array(
            'id' => $robot->id,
            'name' => $robot->name,
        );
    }

    echo json_encode($data);
});


//Searches for robots with $name in their name
$app->get('/api/robots/search/{name}', function($name) use ($app) {

    $phql = "SELECT * FROM Robots WHERE name LIKE :name: ORDER BY name";
    $robots = $app->modelsManager->executeQuery($phql, array(
        'name' => '%' . $name . '%'
    ));

    $data = array();
    foreach ($robots as $robot) {
        $data[] = array(
            'id' => $robot->id,
            'name' => $robot->name,
        );
    }

    echo json_encode($data);

});


//Retrieves robots based on primary key
$app->get('/api/robots/{id:[0-9]+}', function($id) use ($app) {

    $phql = "SELECT * FROM Robots WHERE id = :id:";
    $robot = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ))->getFirst();

    if ($robot == false) {
        $response = array('status' => 'NOT-FOUND');
    } else {
        $response = array(
            'status' => 'FOUND',
            'data' => array(
                'id' => $robot->id,
                'name' => $robot->name
            )
        );
    }

    echo json_encode($response);
});

//Adds a new robot
$app->post('/api/robots', function() use ($app) {

    $robot = json_decode($app->request->getRawBody());

    $phql = "INSERT INTO Robots (name, type, year) VALUES (:name:, :type:, :year:)";

    $status = $app->modelsManager->executeQuery($phql, array(
        'name' => $robot->name,
        'type' => $robot->type,
        'year' => $robot->year
    ));

    //Check if the insertion was successful
    if ($status->success() == true) {

        $this->response->setStatusCode(201, "Created")->sendHeaders();

        $robot->id = $status->getModel()->id;

        $response = array('status' => 'OK', 'data' => $robot);

    } else {

        //Change the HTTP status
        $this->response->setStatusCode(409, "Conflict")->sendHeaders();

        //Send errors to the client
        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response = array('status' => 'ERROR', 'messages' => $errors);

    }

    echo json_encode($response);

});

//Updates robots based on primary key
$app->put('/api/robots/{id:[0-9]+}', function($id) use($app) {

    $robot = json_decode($app->request->getRawBody());

    $phql = "UPDATE Robots SET name = :name:, type = :type:, year = :year: WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
        'name' => $robot->name,
        'type' => $robot->type,
        'year' => $robot->year
    ));

    //Check if the insertion was successful
    if ($status->success() == true) {

        $response = array('status' => 'OK');

    } else {

        //Change the HTTP status
        $this->response->setStatusCode(409, "Conflict")->sendHeaders();

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response = array('status' => 'ERROR', 'messages' => $errors);

    }

    echo json_encode($response);

});

//Deletes robots based on primary key
$app->delete('/api/robots/{id:[0-9]+}', function($id) use ($app) {

    $phql = "DELETE FROM Robots WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ));
    if ($status->success() == true) {

        $response = array('status' => 'OK');

    } else {

        //Change the HTTP status
        $this->response->setStatusCode(409, "Conflict")->sendHeaders();

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response = array('status' => 'ERROR', 'messages' => $errors);

    }

    echo json_encode($response);

});

?>