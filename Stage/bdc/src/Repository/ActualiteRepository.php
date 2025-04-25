<?php

namespace App\Repository;

use App\Entity\Actualite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ActualiteRepository
 */
class ActualiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actualite::class);
    }

    public function findByFilter(string $q)
    {
        $queryBuilder = $this
            ->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
        ;

        if ($q) {
            $queryBuilder
                ->orWhere('a.titre LIKE :q')
                ->orWhere('a.libelle LIKE :q')
                ->setParameter('q', "%$q%");
        }

        return $queryBuilder->getQuery();
    }
}