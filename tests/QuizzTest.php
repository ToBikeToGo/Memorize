<?php

namespace App\Tests;

use App\Entity\Card;
use Doctrine\Persistence\ObjectManager;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class QuizzTest extends ApiTestCase
{
    private static Client $client;
    private static ?EntityRepository $cardRepository = null;


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
    public function testGetQuizzWithoutDoneCategory(): void
    {
        $response = self::$client->request('GET', '/api/cards/quizz');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonResponse($response->getContent());
        $this->assertStringNotContainsString('DONE', $response->getContent());
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