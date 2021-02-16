<?php

namespace App\Repository;

use App\Entity\ArticleReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArticleReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleReference[]    findAll()
 * @method ArticleReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleReferenceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArticleReference::class);
    }
}
