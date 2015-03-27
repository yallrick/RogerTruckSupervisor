<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{

    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/jfeejzfjezjfo")
     * @Method("POST")
     * @Template()
     */
    public function sendAction()
    {

        $response = new JsonResponse();
        $array_documents = array();

        // TODO

        $testObject = ParseObject::create("TestObject");
        $testObject->set("foo", "bar");
        $testObject->save();

        $response->setContent(json_encode( $array_documents ));
        return $response;
    }


    /**
     * @Route("/truck/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getTruckInfo()
    {
        $response = new JsonResponse();
        $array_documents = array();

        // TODO
        $query = new ParseQuery("Camion");
        try {
          $gameScore = $query->get($id);
          // The object was retrieved successfully.
        } catch (ParseException $ex) {
          // The object was not retrieved successfully.
          // error is a ParseException with an error code and message.
        }
        $model = $gameScore->get("model")
        $response->setContent(json_encode( "ok" ));
        return $response;
    }
}
