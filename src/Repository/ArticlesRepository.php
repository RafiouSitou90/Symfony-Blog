<?php

namespace App\Repository;

use App\Entity\Articles;
use App\Entity\Tags;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    // /**
    //  * @return Articles[] Returns an array of Articles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
//     * @param int $page
//     * @param Tags|null $tag
//     * @return Paginator
//     * @throws Exception
     */
/*    public function findAllLatest(int $page = 1, ?Tags $tag = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('author', 'tags', 'category', 'comments', 'ratings')
            ->innerJoin('a.author', 'author')
            ->join('a.category', 'category')
            ->leftJoin('a.tags', 'tags')
            ->leftJoin('a.comments', 'comments')
            ->leftJoin('a.ratings', 'ratings')
            ->where('a.publishedAt <= :now')
            ->andWhere('a.articleStatus = :status')
            ->orderBy('a.publishedAt', 'DESC')
            ->setParameter('now', new DateTime('now'))
            ->setParameter('status', Articles::PUBLISHED())
        ;

        if (null !== $tag) {
            $qb->andWhere(':tag MEMBER OF a.tags')->setParameter('tag', $tag);
        }

        return (new Paginator($qb))->paginate($page);
    }
    */


    public function findAllLatest (?Tags $tag = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('author', 'tags', 'category', 'comments', 'ratings')
            ->innerJoin('a.author', 'author')
            ->join('a.category', 'category')
            ->leftJoin('a.tags', 'tags')
            ->leftJoin('a.comments', 'comments')
            ->leftJoin('a.ratings', 'ratings')
            ->where('a.publishedAt <= :now')
            ->andWhere('a.articleStatus = :status')
            ->orderBy('a.publishedAt', 'DESC')
            ->setParameter('now', new DateTime('now'))
            ->setParameter('status', Articles::PUBLISHED())
        ;

        if (null !== $tag) {
            $qb->andWhere(':tag MEMBER OF a.tags')->setParameter('tag', $tag);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $slug
     * @return Articles|null
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('author', 'tags', 'comments', 'category', 'ratings', 'commentResponses')
            ->innerJoin('a.author', 'author')
            ->join('a.category', 'category')
            ->leftJoin('a.comments', 'comments')
            ->leftJoin('a.tags', 'tags')
            ->leftJoin('a.ratings', 'ratings')
            ->leftJoin('comments.commentResponses', 'commentResponses')
            ->where('a.slug = :slug')
            ->andWhere('a.articleStatus = :status')
            ->orderBy('a.publishedAt', 'DESC')
            ->setParameter('slug', $slug)
            ->setParameter('status', Articles::PUBLISHED())
            ->getQuery()->getOneOrNullResult()
        ;
    }
}
