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
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\AnswerController;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\QuizzController;
use Symfony\Component\Serializer\Annotation\Groups;




#[ORM\Entity(repositoryClass: CardRepository::class)]
#[ApiFilter(
    SearchFilter::class, properties: [
        'tag' => SearchFilter::STRATEGY_IPARTIAL,
    ]
)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/cards/quizz',
            controller: QuizzController::class,
            normalizationContext: ['groups' => ['card:read']],
            denormalizationContext: ['groups' => ['card:read']],

        ),
        new GetCollection(
            normalizationContext: ['groups' => ['card:read']],
            denormalizationContext: ['groups' => ['card:read']],

        ),
        new Post(
            uriTemplate: '/cards',
            controller: CardController::class,
            normalizationContext: ['groups' => ['card:read']],
            denormalizationContext: ['groups' => ['card:read']],
            input: false,


        ),
        new Patch(
            uriTemplate: '/cards/{id}/answer',
            controller: AnswerController::class,
            normalizationContext: ['groups' => ['card:read']],
            denormalizationContext: ['groups' => ['card:read']],
            input: false,
            read: false,

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
    #[Groups(['card:read'])]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['card:read'])]
    private ?string $answer = null;

    #[ORM\Column(length: 255)]
    #[Groups(['card:read'])]
    private ?string $category = "FIRST";

    #[ORM\Column(length: 255)]
    #[Groups(['card:read'])]
    private ?string $tag = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastTimeUsed = null;

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

    public function getLastTimeUsed(): ?\DateTimeInterface
    {
        return $this->lastTimeUsed;
    }

    public function setLastTimeUsed(?\DateTimeInterface $lastTimeUsed): static
    {
        $this->lastTimeUsed = $lastTimeUsed;

        return $this;
    }
}
