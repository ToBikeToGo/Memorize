<?php

namespace App\Tests;

use App\Entity\Card;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AnswerTest extends ApiTestCase
{
    private static Client $client;

    private static $cardRepository;

    private string $answerRoute = '/api/cards/1/answer';

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

    public function testCorrectAnswer(): void
    {
        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => 'application/merge+patch+json'],
            'json' => [
                'isValid' => true
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(204);
    }

    public function testWrongAnswer(): void
    {
        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => 'application/merge+patch+json'],
            'json' => [
                'isValid' => false
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(204);

        $card = self::$cardRepository->find(1);
        $this->assertEquals('FIRST', $card->getCategory());
    }

    public function testInvalidAnswer(): void
    {
        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => 'application/merge+patch+json'],
            'json' => [
                'isValid' => 'invalid'
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => 'application/merge+patch+json'],
            'json' => [
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testNotFoundCard(): void
    {
        self::$client->request('PATCH', '/api/cards/100/answer', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'answer' => true
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
