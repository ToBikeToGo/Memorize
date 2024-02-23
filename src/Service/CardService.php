<?php

namespace App\Service;

use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class CardService
{
    public function validateDate($date, $validator){
        if ($date) {
            $constraints = new Assert\Date();
            $violations = $validator->validate($date, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                return new JsonResponse(json_encode(['errors' => $errors]), Response::HTTP_BAD_REQUEST);
            }
        } else {
            return date('Y-m-d');
        }
    }
    public function isQuizzDoneForDate(array $cards, DateTime $date): bool
    {
        foreach ($cards as $card) {
            if ($date == $card->getLastTimeUsed()) {
                return false;
            }
        }
        return true;
    }
}