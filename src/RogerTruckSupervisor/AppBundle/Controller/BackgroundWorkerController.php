<?php

namespace RogerTruckSupervisor\AppBundle\Controller;

use Parse\ParseClient;
use Parse\ParseException;
use Parse\ParseInstallation;
use Parse\ParsePush;
use Parse\ParseQuery;
use Parse\ParseUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BackgroundWorkerController extends Controller
{
    /**
     * @Route("/checkLocations")
     * @Template()
     */
    public function checkLocationsAction()
    {
        
        $foo = function(){
            $LIMITATIONMAXIMUMDELADUREEDUTEMPSDELARRET = 300;
            $countTrucksStopped = 0;

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
                    $queryLocations->equalTo("camionId", $truck);

                    $locations = $queryLocations->find();

                    $previousLocation = null;
                    $sumTick = 0;
                    for ($j = 0; $j < count($locations); $j++) {
                        $location = $locations[$j];
                        $previousLocation = $j >= 1 ? $locations[$j - 1] : null;

                        if($previousLocation != null &&
                            round($location->get('location')->getLatitude(), 4) == round($previousLocation->get('location')->getLatitude(), 4) &&
                            round($location->get('location')->getLongitude(), 4) == round($previousLocation->get('location')->getLongitude(), 4)
                        ){
                            $sumTick += $location->get('lastTick') - $previousLocation->get('lastTick');
                        }else{
                            $sumTick = 0;
                        }

                        if($sumTick >= $LIMITATIONMAXIMUMDELADUREEDUTEMPSDELARRET){


                            /*/ retrouver un chauffeur
                            $queryDriver = new ParseQuery("Camioneur");
                            $queryDriver->equalTo("camionId", $truck->getObjectId());
                            $driver = $queryDriver->find();
                            
                            // retrouver l'user
                            $queryDriver = new ParseQuery("User");
                            $user = $queryDriver->get($truck->getObjectId());

                            
                            // retrouver l'installation
                            $queryDriver = new ParseQuery("installation");
                            $install = $queryDriver->get($user->get('installationId'));
                            /**/

                            // Notification for Android users
                            $queryAndroid = ParseInstallation::query();
                            $queryAndroid->equalTo('deviceType', 'android');

                            ParsePush::send(array(
                                "where" => $queryAndroid, // $install
                                "data" => array(
                                    "alert" => "Bonjour, nous venons de constater un array de votre véhicule. Nous attendons votre rapport."
                                )
                            ));

                            $truck->set('status', "STOPPED");
                            $truck->save();
                            break;
                        }

                    }
                    

                }


            } catch (ParseException $ex) {  }
            
        };

        $this->logIntoParseFacebook($foo);
        

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
