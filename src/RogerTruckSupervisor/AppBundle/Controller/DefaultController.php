<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseGeoPoint;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route(  "/truck/{id_truck}/location")
     * @Method("POST")
     */
    public function truckUpdateLocationAction(Request $request, $id_truck){
        
        $foo = function($user, $options){
            $idTruck = $options['id_truck'];
            $request = $options['request'];

            $json = new JsonResponse();

            $query = new ParseQuery("Camion");
            $camion_location = new ParseObject("Camion_location");
            try {

                $gameScore = $query->get($idTruck);

                $location = new ParseGeoPoint($request->get('lon') + 0, $request->get('lat') + 0);
                $time = time();
                
                // mémorisation dans l'historique
                $camion_location->set("camionID", $idTruck);
                $camion_location->set("location", $location);
                $camion_location->set("lastTick", $time);

                // mise à jour de la table
                $gameScore->set("location", $location);
                $gameScore->set("lastTick", $time );
                $gameScore->save();
                
                $json->setContent(json_encode(array("operation" => true)));
            } catch (ParseException $ex) {
                
                
                $json->setContent(json_encode(array("operation" => false, "error" => $ex->getMessage())));
            }
            
            return $json;
        };
        return $this->logIntoParseFacebook($foo, array('request' => $request, 'id_truck' => $id_truck));
    }
    
    
    
    
    
    
    



    /**
     * @Route("/truck/{id}/help/{needed}")
     * @Method("POST")
     */
    public function needHelpAction(Request $request, $id, $needed){
        
        $foo = function($user, $options){
            $idTruck = $options['id_truck'];
            $needHelp = $options['needed'];

            $json = new JsonResponse();

            $query = new ParseQuery("Camion");
            try {

                $gameScore = $query->get($idTruck);
                $gameScore->set('status', $needHelp ? "NEED_ASSISTANCE" : "RUNNING");
                $gameScore->save();

                $json->setContent(json_encode(array("operation" => true)));
            } catch (ParseException $ex) {


                $json->setContent(json_encode(array("operation" => false, "error" => $ex->getMessage())));
            }
            
            return $json;
        };
        
        return $this->logIntoParseFacebook($foo, array('request' => $request, 'id_truck' => $id, 'needed' => $needed));
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
