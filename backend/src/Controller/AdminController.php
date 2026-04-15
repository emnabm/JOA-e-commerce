<?php

namespace App\Controller;

use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin')]
    public function index(
        ProductsRepository $productsRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        UserRepository $userRepository
    ): JsonResponse
    {
        $products = $productsRepository->findAll();
        $orders = $orderRepository->findAll();
        $users = $userRepository->findAll();

        $totalStock = 0;
        $stockOutOf = 0;

        foreach ($products as $product) {
            $stock = $product->getStock(); // ou getQuantity() selon ton entité
            $totalStock += $stock;
            if ($stock === 0) {
                $stockOutOf++;
            }
        }
        $soldUnit=$orderItemRepository->findSoldUnit();

        return $this->json([
            'products' => count($products),
            'orders' => count($orders),
            'users' => count($users),
            'totalStock' => $totalStock,
            'stockOutOf' => $stockOutOf,
            'soldUnit' => $soldUnit
        ]);
    }

    #[Route('/admin/top-products', name: 'admin_top_products', methods: ['GET'])]
    public function topProducts(OrderItemRepository $orderItemRepository): JsonResponse
    {
        $topProductsData = $orderItemRepository->findTop8ProductsBySoldQuantity();

        // Calculer popularité simple par exemple
        $maxSales = 0;
        foreach ($topProductsData as $product) {
            if ($product['totalSold'] > $maxSales) {
                $maxSales = $product['totalSold'];
            }
        }

        $topProducts = array_map(function($product) use ($maxSales) {
            return [
                'name' => $product['nom'],
                'sales' => (int) $product['totalSold'],
                'popularity' => $maxSales > 0 ? intval(($product['totalSold'] / $maxSales) * 100) : 0,
            ];
        }, $topProductsData);

        return $this->json($topProducts);
    }

    #[Route('/admin/orders', name: 'admin_orders', methods: ['GET'])]
    public function adminOrders(OrderRepository $orderRepository): JsonResponse
    {
        $orders = $orderRepository->findAll();
        return $this->json($orders,200,[],['groups' => ['order:read']]);
    }
}
