<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParsePush;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BackOfficeController extends Controller
{
    
    
    
    private $user;
    
    /**
     * @Route("/listTechnicians")
     * @Template()
     */
    public function listTechniciansAction()
    {
        $foo = function(){
            $query = new ParseQuery("Technicien");
            try{
                return $query->find();
            } catch(ParseException $ex){
                return array();
            }
        };

        $technicians = $this->logIntoParseFacebook($foo);


        $array_technicians = array();
        foreach($technicians as $technician){
            $iduser = $technician->get('userId');

            $queryUser = ParseUser::query();
            
            $user = $queryUser->get($iduser->getObjectId());
            $username = $user->get('username');
            $email = $user->get('email');

            $queryIntervention = new ParseQuery("Intervention");

            $queryIntervention->equalTo("technicienId", $technician);
            $interventions = $queryIntervention->descending("createdAt");
            $intervention = $interventions->first();

            $available = $intervention->get('dateEndIntervention') ? true : false;

            $array_technicians[] = array("nom" => $username, "email" => $email, "status" => $available);
        } 

        return array(
                "technicians" => $array_technicians
            );    }

    /**
     * @Route("/listDrivers", name="listDrivers")
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

        //$truck->set("status", "ASSISTANCE_CALLED");
        $truck->save();
        
        
        // sélectionner un technicien disponible
        if($truck != null){
            
            $foo = function($usr, $options){
                
                $truck = $options['truck'];


                $technicienQuery = new ParseQuery("Technicien");
                $technicien = $technicienQuery->get("GQFwOCgqSn");

                $fiche = new ParseObject("FicheIntervention");
                $fiche->set("camionId", $truck);
                $fiche->save();
                
                $intervention = new ParseObject("Intervention");
                $intervention->set("coordinate", $truck->get('location'));
                $intervention->set("ficheInterventionId", $fiche);
                $intervention->set("technicienId", $technicien); // identifiant du technicien
                $intervention->save();
                
                return $intervention;
            };
            $intervention = $this->logIntoParseFacebook($foo, array('truck' => $truck));


            // Notification for technician
            $queryIos = ParseInstallation::query();
            $queryIos->equalTo('deviceType', 'ios');

            ParsePush::send(array(
                "where" => $queryIos, // $install
                "data" => array(
                    "alert" => "Bonjour, nous avons besoin de votre aide sur un camion. #911"
                )
            ));



            return $this->redirect($this->generateUrl('listDrivers'));
            //return new Response("Les secours ont été appelé (".$intervention->getObjectId().")");
        }

        return new Response("Un Problème est survennu, impossible d'appeler les secours.");
    }




    private function logIntoParseFacebook($whattodo, array $options = null){
        ParseClient::initialize('oL1kxNThX8882iThZhowKQgGMtcX9u93fMYZzRhc', 'OPMvbyuFsR91hkftjr98w80GRpu1HuY4j9DGI7pC', '2zWN6c32DFiFrY4zch0yhLI7dHXHefluRYf8O1ZV');
        try{
            $user = ParseUser::logIn("simon", "simon");
            $this->user = $user;
            return $whattodo($user, $options);
        }catch(ParseException $error){

        }

        return array();
    }
}
