<?php

namespace App\Tests;

use App\Entity\Card;
use App\Entity\Shop;
use App\Entity\Franchise;
use Doctrine\Persistence\ObjectManager;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CardTest extends ApiTestCase
{
    private static Client $client;

    private static $cardRepository;

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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCards(): void
    {
        $response = self::$client->request('GET', '/api/cards');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonResponse($response->getContent());
    }


    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateCard(): void
    {
        $response = self:: $client->request('POST', '/api/cards', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'tag' => 'geography',
                'category' => 'FIRST',
                'question' => 'What is the capital of France?',
                'answer' => 'Paris',
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonResponse($response->getContent());
    }


    public function testCannotCreateCardWithoutAnswer(): void
    {
        $response = self:: $client->request('POST', '/api/cards', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'tag' => 'geography',
                'category' => 'FIRST',
                'question' => 'What is the capital of France?',
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }


    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testInitialCardShouldBeOne(): void
    {

        $response = self::$client->request('POST', '/api/cards', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'tag' => 'geography',
                'question' => 'What is the capital of France?',
                'answer' => 'Paris',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonResponse($response->getContent());

        $expectedCategory = "FIRST";
        $expectedTag = 'geography';
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals($expectedCategory, $responseArray['category']);
        $this->assertEquals($expectedTag, $responseArray['tag']);
    }


    /**
     * Asserts that the response is a JSON response.
     *
     * @param string $responseContent
     * @return void
     */
    private function assertJsonResponse(string $responseContent): void
    {
        $this->assertJson($responseContent);
    }
}