<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * UtilisateurRepository
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function findByFilter(string $q)
    {
        $queryBuilder = $this
            ->createQueryBuilder('u')
            ->leftJoin('u.access', 'a')
            ->orderBy('u.nom')
            ->addOrderBy('u.prenom')
        ;

        if ($q) {
            $queryBuilder
                ->orWhere('u.nom LIKE :q')
                ->orWhere('u.prenom LIKE :q')
                ->orWhere('u.role LIKE :q')
                ->orWhere('u.login LIKE :q')
                ->orWhere('u.email LIKE :q')
                ->orWhere('u.telephone LIKE :q')
                ->setParameter('q', "%$q%");
        }

        return $queryBuilder->getQuery();
    }
}