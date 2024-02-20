<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Card;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsController]
class QuizzController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Used to fetch all cards for a quizz at a given date. If no date is provided, quizz will be for today.
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function __invoke(Request $request, ValidatorInterface $validator): Response
    {
        $date = $request->query->get('date');
        if ($date) {
            $constraints = new Assert\Date();
            $violations = $validator->validate($date, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                return new Response(json_encode(['errors' => $errors]), Response::HTTP_BAD_REQUEST);
            }
        } else {
            $date = date('Y-m-d');
        }

        $repository = $this->entityManager->getRepository(Card::class);

        $queryBuilder = $repository->createQueryBuilder('c');
        $queryBuilder->where('c.category != :category')
            ->setParameter('category', 'DONE')
            ->orderBy('c.id', 'ASC');

        $cards = $queryBuilder->getQuery()->getResult();
        $response = [];
        foreach ($cards as $card) {
            $response[] = [
                'id' => $card->getId(),
                'question' => $card->getQuestion(),
                'answer' => $card->getAnswer(),
                'category' => $card->getCategory(),
                'tag' => $card->getTag(),
            ];
        }

        return new Response(json_encode($response), Response::HTTP_OK);
    }
}