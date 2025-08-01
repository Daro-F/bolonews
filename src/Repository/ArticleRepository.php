<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function searchByTerm(string $term): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.categorie', 'c')
            ->where('a.titre LIKE :term')
            ->orWhere('a.chapeau LIKE :term')
            ->orWhere('c.libelle LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }
}
