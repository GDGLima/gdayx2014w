<?php

class IndexController extends \Phalcon\Mvc\Controller {

   public function indexAction()    {
       
       $string = "Hello World!";
	   $this->view->setVar("string", $string);
   }

}

?>

