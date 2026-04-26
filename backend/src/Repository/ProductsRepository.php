<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }
    public function findSuggestions(Products $product, int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :cat')
            ->andWhere('p.id != :id')
            ->setParameter('cat', $product->getCategory())
            ->setParameter('id', $product->getId())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * Récupère les produits dans l'ordre des IDs donnés
     */
    public function findByIdsOrdered(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $products = $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        // Trier selon l'ordre des IDs
        $sorted = [];
        foreach ($ids as $id) {
            foreach ($products as $product) {
                if ($product->getId() == $id) {
                    $sorted[] = $product;
                    break;
                }
            }
        }
        return $sorted;
    }

    /**
     * Meilleurs produits par note moyenne
     */
    public function findBestRated(int $limit = 8, array $exclude = []): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->groupBy('p.id')
            ->orderBy('AVG(r.rating)', 'DESC')
            ->setMaxResults($limit);

        if (!empty($exclude)) {
            $qb->andWhere('p.id NOT IN (:exclude)')
               ->setParameter('exclude', $exclude);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Produits par catégories
     */
    public function findByCategoryIds(array $categoryIds, array $exclude = [], int $limit = 8): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        $qb = $this->createQueryBuilder('p')
            ->where('p.category IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds)
            ->andWhere('p.stock > 0')
            ->orderBy('p.prix', 'ASC')
            ->setMaxResults($limit);

        if (!empty($exclude)) {
            $qb->andWhere('p.id NOT IN (:exclude)')
               ->setParameter('exclude', $exclude);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Produits similaires
     */
    public function findSimilarProducts(int $productId, array $exclude = [], int $limit = 8): array
    {
        $product = $this->find($productId);
        if (!$product) {
            return [];
        }

        $qb = $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.id != :productId')
            ->setParameter('category', $product->getCategory())
            ->setParameter('productId', $productId)
            ->orderBy('p.prix', 'ASC')
            ->setMaxResults($limit);

        if (!empty($exclude)) {
            $qb->andWhere('p.id NOT IN (:exclude)')
               ->setParameter('exclude', $exclude);
        }

        return $qb->getQuery()->getResult();
    }
}