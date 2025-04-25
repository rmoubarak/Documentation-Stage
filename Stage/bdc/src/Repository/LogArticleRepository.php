<?php

namespace App\Repository;

use App\Entity\LogArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogArticle>
 */
class LogArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogArticle::class);
    }

    //    /**
    //     * @return LogArticle[] Returns an array of LogArticle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LogArticle
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
