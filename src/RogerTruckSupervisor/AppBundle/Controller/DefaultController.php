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
use Symfony\Component\HttpFoundation\Response;

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
    
    private function logIntoParseFacebook($whattodo, array $options = null){
        ParseClient::initialize('oL1kxNThX8882iThZhowKQgGMtcX9u93fMYZzRhc', 'OPMvbyuFsR91hkftjr98w80GRpu1HuY4j9DGI7pC', '2zWN6c32DFiFrY4zch0yhLI7dHXHefluRYf8O1ZV');
        try{
            $user = ParseUser::logIn("simon", "simon");
            return $whattodo($user, $options);
        }catch(ParseException $error){

        }
        
        return new Response("ERROR !! ça marche pas !");
    }

    /**
     * @Route("/jfeejzfjezjfo")
     * @Method("GET")
     * @Template()
     */
    public function sendAction()
    {

        $whatareyougonnadonow = function($user){

            $response = new JsonResponse();
            $array_documents = array();
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
            
        };
        return $this->logIntoParseFacebook($whatareyougonnadonow);

    }

    /**
     * @Route(  "/truck/{id_truck}/location")
     * @Method("POST")
     */
    public function truckUpdateLocationAction($id_truck){
        
        $foo = function($user, $options){
            $idTruck = $options['id_truck'];
            
            return new Response($idTruck);
        };
        return $this->logIntoParseFacebook($foo, array('id_truck' => $id_truck));
        
    }
    
    


    /**
     * @Route("/truck/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getTruckInfo($id)
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
        $model = $gameScore->get("model");
        $response->setContent(json_encode( $model ));
      
        return new Response();
    }
}
