<?php

namespace App\Entity;

use ApiPlatform\Metadata\GetCollection;
use App\Controller\CardController;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CardRepository;
use App\Controller\AnswerController;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\QuizzController;



#[ORM\Entity(repositoryClass: CardRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/cards/quizz',
            controller: QuizzController::class
        ),
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/cards',
            controller: CardController::class,
            input: false

        ),
        new Patch(
            uriTemplate: '/cards/{id}/answer',
            read: false,
            input: false,
            controller: AnswerController::class
        )
    ]
)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $answer = null;

    #[ORM\Column(length: 255)]
    private ?string $category = "FIRST";

    #[ORM\Column(length: 255)]
    private ?string $tag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }
}
