<?php
// src/Service/RecommendationService.php

namespace App\Service;

use App\Entity\User;
use App\Repository\ProductsRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    public function __construct(
        private ProductsRepository $productRepo,
        private OrderRepository $orderRepo,
        private EntityManagerInterface $em
    ) {}

    public function getPurchaseBasedRecommendations(?User $user, int $currentId = 0, int $limit = 8): array
    {
        // Fallback pour utilisateur non connecté
        if (!$user) {
            return $this->productRepo->findBestRated($limit, [$currentId]);
        }

        try {
            // Méthode simplifiée avec QueryBuilder de Doctrine
            $qb = $this->em->createQueryBuilder();
            
            // Récupérer les catégories des produits achetés par l'utilisateur
            $subQuery = $this->em->createQueryBuilder()
                ->select('IDENTITY(oi.product)')
                ->from('App\Entity\Order', 'o')
                ->join('o.orderItems', 'oi')
                ->where('o.user = :user');
            
            // Récupérer les catégories de ces produits
            $categoryQb = $this->em->createQueryBuilder();
            $categoryQb->select('IDENTITY(p.category)')
                ->from('App\Entity\Products', 'p')
                ->where($categoryQb->expr()->in('p.id', $subQuery->getDQL()))
                ->setParameter('user', $user);
            
            // Produits recommandés
            $qb->select('p')
                ->from('App\Entity\Products', 'p')
                ->where($qb->expr()->in('p.category', $categoryQb->getDQL()))
                ->andWhere('p.id != :currentId')
                ->andWhere('p.stock > 0')
                ->setParameter('currentId', $currentId)
                ->orderBy('p.prix', 'ASC')
                ->setMaxResults($limit);
            
            $recommendations = $qb->getQuery()->getResult();
            
            if (empty($recommendations)) {
                return $this->productRepo->findBestRated($limit, [$currentId]);
            }
            
            return $recommendations;
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner les meilleurs produits
            return $this->productRepo->findBestRated($limit, [$currentId]);
        }
    }

    public function getHistoryBasedRecommendations(array $historyIds, int $currentId = 0, int $limit = 8): array
    {
        $ids = array_filter($historyIds, fn($id) => $id != $currentId);
        $ids = array_slice($ids, 0, $limit);

        if (empty($ids)) {
            return [];
        }

        return $this->productRepo->findByIdsOrdered($ids);
    }

    public function getSimilarProducts(int $productId, int $limit = 6): array
    {
        $product = $this->productRepo->find($productId);
        
        if (!$product) {
            return [];
        }
        
        $category = $product->getCategory();
        
        if (!$category) {
            return $this->productRepo->findBestRated($limit, [$productId]);
        }
        
        return $this->productRepo->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.id != :productId')
            ->andWhere('p.stock > 0')
            ->setParameter('category', $category)
            ->setParameter('productId', $productId)
            ->orderBy('p.prix', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTrendingProducts(int $limit = 8): array
    {
        return $this->productRepo->findBestRated($limit);
    }
}