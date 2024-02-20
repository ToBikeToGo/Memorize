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

    private string $answerRoute = '/cards/1/answer';

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

    public function testCorrectAnswer(): void
    {
        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
            'json' => [
                'isValid' => true
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(204);
    }

    public function testWrongAnswer(): void
    {
        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
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
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
            'json' => [
                'isValid' => 'invalid'
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
            'json' => [
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testNotFoundCard(): void
    {
        self::$client->request('PATCH', '/api/cards/100/answer', [
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
            'json' => [
                'answer' => true
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }


    public function testOnCorrectAnswerCardCategoryIsUpdated(): void
    {
        $firstCard = self::$cardRepository->findOneBy(['category' => 'FIRST']);

        if (!$firstCard) {
            $response = self::$client->request('POST', '/api/cards', [
                'headers' => ['Content-Type' => 'application/ld+json'],
                'json' => [
                    'tag' => 'geography',
                    'question' => 'What is the capital of France?',
                    'answer' => 'Paris',
                ]
            ]);
            $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
            $firstCard = self::$cardRepository->findOneBy(['category' => 'FIRST']);
        }

        $response = self::$client->request('PATCH', '/api/cards/'.$firstCard->getId().'/answer', [
            'headers' => ['Content-Type' => 'application/merge+patch+json'],
            'json' => [
                'isValid' => true
            ]
        ]);

        $this->assertResponseStatusCodeSame(204);
        self::bootKernel()->getContainer()->get('doctrine')->getManager()->clear();

        $updatedCard = self::$cardRepository->find($firstCard->getId());
        $this->assertEquals('SECOND', $updatedCard->getCategory());
    }
}
