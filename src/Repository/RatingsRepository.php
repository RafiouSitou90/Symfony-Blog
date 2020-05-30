<?php

namespace App\Repository;

use App\Entity\Ratings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ratings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ratings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ratings[]    findAll()
 * @method Ratings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ratings::class);
    }

    // /**
    //  * @return Ratings[] Returns an array of Ratings objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ratings
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
