<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseGeoPoint;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParsePush;
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
                $camion_location->save();

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
     * @Route("/pushTechnician")
     * @Method("GET")
     * @Template()
     */
    public function pushTechnicianAction()
    {
        $foo = function(){
            $json = new JsonResponse();
            try{
                $queryIOS = ParseInstallation::query();
                $queryIOS->equalTo('deviceType', 'ios');
                 
                ParsePush::send(array(
                  "where" => $queryIOS,
                  "data" => array(
                    "alert" => "HELLO IOS"
                  )
                ));
                $json->setContent(json_encode(array("operation" => true)));
            }
            catch(ParseException $ex){
                $json->setContent(json_encode(array("operation" => false, "error" => $ex->getMessage())));
            }
            return $json;
        };
        return $this->logIntoParseFacebook($foo, null);
    }

        /**
     * @Route("/pushDriver")
     * @Method("GET")
     * @Template()
     */
    public function pushDriverAction()
    {
        $foo = function(){
            $json = new JsonResponse();
            try{
                // Notification for Android users
                $queryAndroid = ParseInstallation::query();
                $queryAndroid->equalTo('deviceType', 'android');
                 
                ParsePush::send(array(
                  "where" => $queryAndroid,
                  "data" => array(
                    "alert" => "Your suitcase has been filled with tiny robots!"
                  )
                ));
                $json->setContent(json_encode(array("operation" => true)));
            }
            catch(ParseException $ex){
                $json->setContent(json_encode(array("operation" => false, "error" => $ex->getMessage())));
            }
            return $json;
        };
        return $this->logIntoParseFacebook($foo, null);
    }
}   
