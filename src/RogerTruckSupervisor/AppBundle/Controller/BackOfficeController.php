<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BackOfficeController extends Controller
{
    /**
     * @Route("/listTechnicians")
     * @Template()
     */
    public function listTechniciansAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/listDrivers")
     * @Template()
     */
    public function listDriversAction()
    {

        $foo = function(){
            
            $query = new ParseQuery("Camion");

            try {
                return $query->find();
            } catch (ParseException $ex) {
                return array();
            }

        };

        $trucks = $this->logIntoParseFacebook($foo);
        
        // transformation de l'objet en liste
        $array_truck = array();
        foreach($trucks as $truck){
            $array_truck[] = array(
                "id" => $truck->getObjectId(),
                "immatriculation" => $truck->get("immatriculation"),
                "status" => $truck->get("status")
            );
        }
        
        
        return array(
                "trucks" => $array_truck
            );    
    }

    /**
     * @Route("/sendTechnician")
     * @Method("POST")
     * @Template()
     */
    public function sendTechnicianAction(Request $request)
    {
        
        // retrouver le camion
        $foo = function($user, $options) {
            $query = new ParseQuery("Camion");

            $idTruck = $options['request']->get('id_truck');
            
            try {
                return $query->get($idTruck);
            } catch (ParseException $ex) {
                return null;
            }
        };
        $truck = $this->logIntoParseFacebook($foo, array('request' => $request));

        $truck->set("status", "RUNNING");
        $truck->save();
        
        
        // sélectionner un technicien disponible
        if($truck != null){
            
            $foo = function($usr, $options){
                
                $truck = $options['truck'];
                
                $intervention = new ParseObject("Intervention");
                $intervention->set("coordinate", $truck->get('location'));
                $intervention->save();
                
                return $intervention;
            };
            $intervention = $this->logIntoParseFacebook($foo, array('truck' => $truck));
            
            return new Response("Les secours ont été appelé (".$intervention->getObjectId().")");
        }

        return new Response("Un Problème est survennu, impossible d'appeler les secours.");
    }




    private function logIntoParseFacebook($whattodo, array $options = null){
        ParseClient::initialize('oL1kxNThX8882iThZhowKQgGMtcX9u93fMYZzRhc', 'OPMvbyuFsR91hkftjr98w80GRpu1HuY4j9DGI7pC', '2zWN6c32DFiFrY4zch0yhLI7dHXHefluRYf8O1ZV');
        try{
            $user = ParseUser::logIn("simon", "simon");
            return $whattodo($user, $options);
        }catch(ParseException $error){

        }

        return array();
    }
}
