<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseException;
use Parse\ParseQuery;
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
        
        
        
        
        
        return array(
                "trucks" => $trucks
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
