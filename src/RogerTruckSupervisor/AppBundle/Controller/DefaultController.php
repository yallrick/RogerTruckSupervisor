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
     * @Route("/demo")
     * @Method("GET")
     * @Template()
     */
    public function demoAction()
    {

        $response = new JsonResponse();
        $array_documents = array();

        // TODO
        
        $array_documents[] = array("code" => 200, "message" => "touvabien");
        $array_documents[] = array("code" => 200, "message" => "touvabien");
        $array_documents[] = array("code" => 200, "message" => "touvabien");
        $array_documents[] = array("code" => 200, "message" => "touvabien");

        $response->setContent(json_encode( $array_documents ));
        return $response;
    }
}
