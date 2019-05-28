<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RODDashboardControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'rodDashboard');
        $this->assertTrue(true);
    }

    public function testSeting()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'rodDashboard/settings');
        $this->assertTrue(true);
    }

}
