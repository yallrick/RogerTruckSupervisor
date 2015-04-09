<?php

namespace RogerTruckSupervisor\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackgroundWorkerControllerTest extends WebTestCase
{
    public function testChecklocations()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkLocations');
    }

}
