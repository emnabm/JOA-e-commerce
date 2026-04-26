<?php

namespace App\Repository;

use App\Entity\ProductView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductView>
 */
class ProductViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductView::class);
    }

     public function findRecentViewsByUser(int $userId, int $limit = 10): array
    {
        return $this->createQueryBuilder('pv')
            ->select('IDENTITY(pv.product) as productId')
            ->where('pv.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('pv.viewedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getSingleColumnResult();
    }
}
