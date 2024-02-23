<?php

namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class AnswerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Card $card, Request $request): Response
    {
        if (!$card) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $body = json_decode($request->getContent(), true);

        if (!isset($body['isValid']) || !is_bool($body['isValid'])) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if ($body['isValid']) {
            match ($card->getCategory()) {
                'FIRST' => $card->setCategory('SECOND'),
                'SECOND' => $card->setCategory('THIRD'),
                'THIRD' => $card->setCategory('FOURTH'),
                'FOURTH' => $card->setCategory('FIFTH'),
                'FIFTH' => $card->setCategory('SIXTH'),
                'SIXTH' => $card->setCategory('SEVENTH'),
                'SEVENTH' => $card->setCategory('DONE'),
            };

        } else {
            $card->setCategory('FIRST');
        }

        $this->entityManager->persist($card);
        $this->entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
