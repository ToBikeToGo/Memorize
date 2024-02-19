<?php

namespace App\Factory;

use App\Entity\Card;
use App\Repository\CardRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Card>
 *
 * @method        Card|Proxy create(array|callable $attributes = [])
 * @method static Card|Proxy createOne(array $attributes = [])
 * @method static Card|Proxy find(object|array|mixed $criteria)
 * @method static Card|Proxy findOrCreate(array $attributes)
 * @method static Card|Proxy first(string $sortedField = 'id')
 * @method static Card|Proxy last(string $sortedField = 'id')
 * @method static Card|Proxy random(array $attributes = [])
 * @method static Card|Proxy randomOrCreate(array $attributes = [])
 * @method static CardRepository|RepositoryProxy repository()
 * @method static Card[]|Proxy[] all()
 * @method static Card[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Card[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Card[]|Proxy[] findBy(array $attributes)
 * @method static Card[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Card[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class CardFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'answer' => self::faker()->text(),
            'category' => self::faker()->randomNumber(),
            'question' => self::faker()->text(),
            'tag' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Card $card): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Card::class;
    }
}
