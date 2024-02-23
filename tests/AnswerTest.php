<?php

namespace App\Tests;

use App\Entity\Card;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AnswerTest extends ApiTestCase
{
    private static $client;
    private $cardRepository;
    private $entityManager;
    private string $answerRoute = '/cards/1/answer';

    private string $contentType = 'application/json';
    protected function setUp(): void
    {
        parent::setUp();
        self::$client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->cardRepository = $this->entityManager->getRepository(Card::class);

        $this->entityManager->beginTransaction();
        $this->prepareDatabase();
    }

    private function prepareDatabase(): void
    {
        $card = $this->cardRepository->findOneBy(['category' => 'FIRST']);
        if (!$card) {
            $card = new Card();
            $card->setTag('geography');
            $card->setQuestion('What is the capital of France?');
            $card->setAnswer('Paris');
            $card->setCategory('FIRST');
            $this->entityManager->persist($card);
            $this->entityManager->flush();
        }

    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        $this->entityManager->close();
        parent::tearDown();
    }

    public static function initClient(): void
    {
        self::$client = static::createClient();
    }

    public static function initRepository(): void
    {
        $objectManager = self::bootKernel()->getContainer()->get('doctrine')->getManager();

            self::$cardRepository = $objectManager->getRepository(Card::class);


        $loader = new AppFixtures();
        $loader->load($objectManager);
    }

    public function testCorrectAnswer(): void
    {
        self::$client->request('PATCH', $this->answerRoute, [
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
            'json' => ['isValid' => true]
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
        self::$client->request('PATCH', '/api/cards/10000000/answer', [
            'headers' => ['Content-Type' => $this->contentType, 'Accept' => $this->contentType],
            'json' => [
                'answer' => true
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }


    public function testOnCorrectAnswerCardCategoryIsUpdated(): void
    {
        $firstCard = $this->cardRepository->findOneBy(['category' => 'FIRST']);
        $this->assertNotNull($firstCard, 'First category card not found.');

        self::$client->request('PATCH', '/api/cards/'.$firstCard->getId().'/answer', [
            'headers' => ['Content-Type' => 'application/merge+patch+json'],
            'json' => ['isValid' => true]
        ]);

        $this->assertResponseStatusCodeSame(204);

        $updatedCard = $this->cardRepository->find($firstCard->getId());
        $this->assertEquals('SECOND', $updatedCard->getCategory(), 'Card category not updated correctly.');
    }


}
