<?php

namespace App\Controller;

use App\Entity\Card;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;

class CardController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'fields' => [
                'question' => [new Assert\NotBlank()],
                'answer' => [new Assert\NotBlank()],
                'tag' => [new Assert\NotBlank()],
            ],
            'allowExtraFields' => true,
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return new Response(json_encode(['errors' => $errors]), Response::HTTP_BAD_REQUEST);
        }

        $card = new Card();
        $card->setQuestion($data['question']);
        $card->setAnswer($data['answer']);
        $card->setCategory($data['category'] ?? 'FIRST');
        $card->setTag($data['tag']);

     
        $this->entityManager->persist($card);
        $this->entityManager->flush();



        return new Response(json_encode(['id' => $card->getId(),
            'question' => $card->getQuestion(),
            'answer' => $card->getAnswer(),
            'category' => $card->getCategory(),
            'tag' => $card->getTag()
            ]), Response::HTTP_CREATED);
    }
}