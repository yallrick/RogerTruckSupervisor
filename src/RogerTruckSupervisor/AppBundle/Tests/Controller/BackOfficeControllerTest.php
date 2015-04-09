<?php

namespace RogerTruckSupervisor\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackOfficeControllerTest extends WebTestCase
{
    public function testListtechnicians()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/listTechnicians');
    }

    public function testListdrivers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/listDrivers');
    }

    public function testSendtechnician()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sendTechnician');
    }

}
