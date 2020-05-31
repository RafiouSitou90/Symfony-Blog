<?php

namespace App\Repository;

use App\Entity\CommentsResponses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentsResponses|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentsResponses|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentsResponses[]    findAll()
 * @method CommentsResponses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsResponsesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentsResponses::class);
    }

    // /**
    //  * @return CommentsResponses[] Returns an array of CommentsResponses objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentsResponses
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
