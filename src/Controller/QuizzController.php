<?php

namespace App\Controller;

use App\Repository\CardRepository;
use App\Service\CardService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Card;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsController]
class QuizzController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private CardRepository $cardRepository, private CardService $cardService)
    {
    }

    /**
     * Used to fetch all cards for a quizz at a given date. If no date is provided, quizz will be for today.
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {

        $date = $this->cardService->validateDate($request->query->get('date'), $validator);
        $dateTime = new DateTime($date); // Convertir la chaîne en objet DateTime
        $cards = $this->cardRepository->getCardByCategoryAndFrequency($date);
        $isQuizzDone = $this->cardService->isQuizzDoneForDate($cards, $dateTime);
        if (!$isQuizzDone) {
            return new JsonResponse(['error' => 'Un questionnaire a déjà été réalisé pour cette date'], Response::HTTP_BAD_REQUEST);
        }
        $this->setCardUsedForToday($cards, $dateTime);

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

        return new JsonResponse($response, Response::HTTP_OK);
    }

    private function setCardUsedForToday(array $cards, DateTime $date): void
    {
        foreach ($cards as $card) {
            $card->setLastTimeUsed($date);
        }
        $this->entityManager->flush();
    }
}