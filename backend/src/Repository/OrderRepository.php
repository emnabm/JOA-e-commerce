<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    // Retourne les IDs de catégories achetées par l'utilisateur
public function findPurchasedCategoryIdsByUser(User $user): array
{
    // Méthode plus simple et fiable
    $conn = $this->getEntityManager()->getConnection();
    
    $sql = '
        SELECT DISTINCT p.category_id 
        FROM `order` o
        INNER JOIN order_item oi ON oi.order_id = o.id
        INNER JOIN products p ON p.id = oi.product_id
        WHERE o.user_id = :userId
        AND p.category_id IS NOT NULL
    ';
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue('userId', $user->getId());
    $result = $stmt->executeQuery();
    
    $categoryIds = [];
    while ($row = $result->fetchAssociative()) {
        $categoryIds[] = (int)$row['category_id'];
    }
    
    return $categoryIds;
    }
}
