<?php

namespace App\Tests;

use App\Entity\Card;
use DateTime;
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

    public function testOnlyOneQuizzPerDay(): void
    {
        $objectManager = self::bootKernel()->getContainer()->get('doctrine')->getManager();

        // Date donnée pour le test
        $card1 = new Card();
        $card1->setQuestion('First Card Question for testing');
        $card1->setAnswer('First Card Answer for testing');
        $card1->setCategory('FIRST');
        $card1->setTag('Testing');
        $card1->setLastTimeUsed((new DateTime())->modify('-1 day'));

        $card2 = new Card();
        $card2->setQuestion('Second Card Question for testing');
        $card2->setAnswer('Second Card Answer for testing');
        $card2->setCategory('SECOND');
        $card2->setTag('Testing');
        $card2->setLastTimeUsed((new DateTime())->modify('-1 day'));
        $now = date('Y-m-d');
        $objectManager->persist($card1); // Marquez l'entité comme gérée par Doctrine
        $objectManager->persist($card2); // Marquez l'entité comme gérée par Doctrine

// Flush pour enregistrer les modifications dans la base de données
        $objectManager->flush();

        $response1 = self::$client->request('GET', '/cards/quizz?date=' . $now, [
            'headers' => [
                'Accept' => $this->contentType,
                'Content-Type' => $this->contentType,
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // Appel de l'endpoint pour récupérer les cartes pour la deuxième fois avec la même date
        $response2 = self::$client->request('GET', '/cards/quizz?date=' . $now, [
            'headers' => [
                'Accept' => $this->contentType,
                'Content-Type' => $this->contentType,
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetQuizzWithoutDoneCategory(): void
    {
        $response = self::$client->request('GET', '/cards/quizz', [
            'headers' => [
                'Accept' => $this->contentType,
                'Content-Type' => $this->contentType,
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonResponse($response->getContent());
        $this->assertStringNotContainsString('DONE', $response->getContent());
    }

    public function testStartQuizzContainQuestionNotEmpty(): void
    {
        $response = self::$client->request('GET', '/cards/quizz', [
            'headers' => [
                'Accept' => $this->contentType,
                'Content-Type' => $this->contentType,
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($response->getContent(), true);
        foreach ($responseData as $card) {
            $this->assertNotEmpty($card['question'], 'La question ne doit pas être vide');
        }
    }

    public function testGetRightCardByCategoryAndFrequency(): void
    {
        $objectManager = self::bootKernel()->getContainer()->get('doctrine')->getManager();
        $card = $objectManager->getRepository(Card::class)->findOneBy(['category' => 'FIRST']);
        $date = $card->getLastTimeUsed()->modify('+1 day')->format('Y-m-d');;
        $response = self::$client->request('GET', '/cards/quizz?date=' . $date, [
            'headers' => [
                'Accept' => $this->contentType,
                'Content-Type' => $this->contentType,
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonResponse($response->getContent());
        $data = json_decode($response->getContent(), true);
        // Vérifier si la carte attendue est présente dans les données JSON
        $this->assertContains($card->getCategory(), array_column($data, 'category'));
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