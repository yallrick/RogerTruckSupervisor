<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseException;
use Parse\ParseQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class BackgroundWorkerController extends Controller
{
    /**
     * @Route("/checkLocations")
     * @Template()
     */
    public function checkLocationsAction()
    {
        $response = new JsonResponse();
        $array_documents = array();
        $LIMITATIONMAXIMUMDELADUREEDUTEMPSDELARRET = 300;

        // TODO
        $query = new ParseQuery("Camion");
        $query->equalTo("status", "RUNNING");
        
        
        try {
            $trucks = $query->find();

            // Pour tous les camions
            for ($i = 0; $i < count($trucks); $i++) {
                $truck = $trucks[$i];

                // récupérer l'historique des locations
                $queryLocations = new ParseQuery("Camion_locations");
                $queryLocations->equalTo("camionID", $truck->getObjectId());
             
                $locations = $queryLocations->find();
                
                $previousLocation = null;
                $sumTick = 0;
                for ($j = 0; $j < count($locations); $j++) {
                    $location = $locations[$i];

                    // TODO : comparer les GeoPoints
                    if($previousLocation != null && $previousLocation == $previousLocation){
                        $sumTick += $location->get('laskTick') - $previousLocation->get('laskTick');
                    }else{
                        $sumTick = 0;
                    }
                    
                    if($sumTick >= $LIMITATIONMAXIMUMDELADUREEDUTEMPSDELARRET){
                        // TODO : envoyer le message de "qu'es ce qui se passe"


                        $truck->set('status', "STOPPED");
                        break;
                    }
                    
                    $previousLocation = $location;
                }
            }
            

        } catch (ParseException $ex) {  }
        
        

        return array(
                // ...
            );
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


}
