<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BackOfficeController extends Controller
{
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
            $queryUser = new ParseQuery("User");
            
            $user = $queryUser->get($technician->get('userId'));
            $username = $user->get('username');
            $email = $user->get('email');

            $queryIntervention = new ParseQuery("Intervention");
            $queryIntervention->equalTo("technicienId", $technician->getOjectId());
            $intervention = $queryIntervention->find();
            $available = $intervention->get('dateEndIntervention') ? true : false;

            $array_technicians[] = array("nom" => $username, "email" => $email, "status" => $available);
        } 

        return array(
                "technicians" => $array_technicians
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
        
        // trasformation de l'objet en liste
        $array_truck = array();
        foreach($trucks as $truck){
            $array_truck[] = array("immatriculation" => $truck->get("immatriculation"), "status" => $truck->get("status"));
        }
        
        
        return array(
                "trucks" => $array_truck
            );    }

    /**
     * @Route("/sendTechnician")
     * @Template()
     */
    public function sendTechnicianAction()
    {
        return array(
                // ...
            );    }




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
