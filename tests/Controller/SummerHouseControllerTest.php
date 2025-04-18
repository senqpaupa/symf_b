<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SummerHouseControllerTest extends WebTestCase
{
    public function testGetSummerHouses(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/summerhouse/list');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($responseData);

        $this->assertNotEmpty($responseData);

        if (!empty($responseData)) {
            $summerHouse = $responseData[0];

            $this->assertArrayHasKey('id', $summerHouse);
            $this->assertArrayHasKey('address', $summerHouse);
            $this->assertArrayHasKey('price', $summerHouse);
            $this->assertArrayHasKey('bedrooms', $summerHouse);
            $this->assertArrayHasKey('distanceFromSea', $summerHouse);
            $this->assertArrayHasKey('hasShower', $summerHouse);
            $this->assertArrayHasKey('hasBathroom', $summerHouse);
        }
    }
}