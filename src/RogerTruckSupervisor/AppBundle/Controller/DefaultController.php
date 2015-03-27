<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
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
     * @Method("GET")
     * @Template()
     */
    public function sendAction()
    {

        $response = new JsonResponse();
        $array_documents = array();

        // TODO


        ParseClient::initialize('oL1kxNThX8882iThZhowKQgGMtcX9u93fMYZzRhc', 'OPMvbyuFsR91hkftjr98w80GRpu1HuY4j9DGI7pC', '2zWN6c32DFiFrY4zch0yhLI7dHXHefluRYf8O1ZV');

        try {
            $user = ParseUser::logIn("simon", "simon");

            $query = new ParseQuery("Camion");

            $results = $query->find();
            echo "Successfully retrieved " . count($results) . " camion.";

            // Do something with the returned ParseObject values
            for ($i = 0; $i < count($results); $i++) {
                $object = $results[$i];
                echo $object->getObjectId() . ' - ' . $object->get('playerName');
            }

            $response->setContent(json_encode( $array_documents ));
            return $response;
            
            
        } catch (ParseException $error) {
            // The login failed. Check error to see why.
        }

        $query = new ParseQuery("Camion");

        $results = $query->find();
        echo "Successfully retrieved " . count($results) . " camion.";
        
        // Do something with the returned ParseObject values
        for ($i = 0; $i < count($results); $i++) {
            $object = $results[$i];
            echo $object->getObjectId() . ' - ' . $object->get('playerName');
        }

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
