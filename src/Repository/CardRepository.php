<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

//    /**
//     * @return Card[] Returns an array of Card objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Card
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getCardByCategoryAndFrequency(string $date){
        $oneDayAgo = (new \DateTime($date))->modify('-1 day')->format('Y-m-d');
        $twoDaysAgo = (new \DateTime($date))->modify('-2 days')->format('Y-m-d');
        $fourDaysAgo = (new \DateTime($date))->modify('-4 days')->format('Y-m-d');
        $eightDaysAgo = (new \DateTime($date))->modify('-8 days')->format('Y-m-d');
        $sixteenDaysAgo = (new \DateTime($date))->modify('-16 days')->format('Y-m-d');
        $thirtyTwoDaysAgo = (new \DateTime($date))->modify('-32 days')->format('Y-m-d');
        $sixtyFourDaysAgo = (new \DateTime($date))->modify('-64 days')->format('Y-m-d');
        return $this->createQueryBuilder('c')
            ->where('c.category != :category')
            ->andWhere(
                $this->createQueryBuilder('c')
                    ->expr()->orX(
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category1', 'c.lastTimeUsed = :oneDayAgo'),
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category2', 'c.lastTimeUsed = :twoDaysAgo'),
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category3', 'c.lastTimeUsed = :fourDaysAgo'),
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category4', 'c.lastTimeUsed = :eightDaysAgo'),
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category5', 'c.lastTimeUsed = :sixteenDaysAgo'),
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category6', 'c.lastTimeUsed = :thirtyTwoDaysAgo'),
                        $this->createQueryBuilder('c')->expr()->andX('c.category = :category7', 'c.lastTimeUsed = :sixtyFourDaysAgo')
                    )
            )
            ->setParameter('category', 'DONE')
            ->setParameter('category1', 'FIRST')
            ->setParameter('category2', 'SECOND')
            ->setParameter('category3', 'THIRD')
            ->setParameter('category4', 'FOURTH')
            ->setParameter('category5', 'FIFTH')
            ->setParameter('category6', 'SIX')
            ->setParameter('category7', 'SEVENTH')
            ->setParameter('oneDayAgo', $oneDayAgo)
            ->setParameter('twoDaysAgo', $twoDaysAgo)
            ->setParameter('fourDaysAgo', $fourDaysAgo)
            ->setParameter('eightDaysAgo', $eightDaysAgo)
            ->setParameter('sixteenDaysAgo', $sixteenDaysAgo)
            ->setParameter('thirtyTwoDaysAgo', $thirtyTwoDaysAgo)
            ->setParameter('sixtyFourDaysAgo', $sixtyFourDaysAgo)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
