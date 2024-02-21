<?php

namespace App\Tests;

use App\Entity\Card;
use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class TagTest extends ApiTestCase
{
    private static Client $client;

    private static $cardRepository;

    private string $contentType = 'application/json';

    public function setUp(): void
    {
        self::initClient();
        self::initRepository();
    }
    
    public static function initClient(): void
    {
        self::$client = static::createClient();
    }

    public static function initRepository(): void
    {
        $objectManager = self::bootKernel()->getContainer()->get('doctrine')->getManager();

        if (null == self::$cardRepository) {
            self::$cardRepository = $objectManager->getRepository(Card::class);
        }
    }

    public function testGetCardsByTag(): void
    {
        self::$client->request('GET', '/cards?tag=animal', [
            'headers' => ['Accept' => $this->contentType]
        ]);
        
        $this->assertResponseIsSuccessful();

        $content = self::$client->getResponse()->getContent();

        $this->assertJson($content);

        $data = json_decode($content, true);

        $this->assertArrayHasKey('tag', $data[0]);

        $this->assertEquals('animal', $data[0]['tag']);

    }

}
