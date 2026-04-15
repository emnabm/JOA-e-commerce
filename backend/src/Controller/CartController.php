<?php

namespace App\Controller;

use App\Entity\Items;
use App\Entity\Panier;
use App\Repository\ItemsRepository;
use App\Repository\PanierRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/api/getCart', name: 'app_getCart', methods: ['GET'])]
    public function index(ItemsRepository $itemsRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not found'], 401);
        }

        // Récupérer les items
        $items = $itemsRepository->findBy(['user' => $user]);
        if (empty($items)) {
            return $this->json(['error' => 'Items not found'], 404);
        }

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'id' => $item->getId(),
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $item->getProduct()->getId(),
                    'nom' => $item->getProduct()->getNom(),
                    'prix' => $item->getProduct()->getPrix(),
                    'image' => $item->getProduct()->getImage(),
                ],
            ];
        }

        return $this->json([
            'items' => $result,
        ]);
    }
    #[Route('/api/addToCart', name: 'app_addToCart', methods: ['POST'])]
    public function add(
        Request $request,
        ProductsRepository $productsRepository,
        ItemsRepository $itemsRepository,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not found'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId || $quantity < 1) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $product = $productsRepository->find($productId);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        if ($product->getStock() < $quantity) {
            return new JsonResponse(['error' => 'Invalid quantity'], 400);
        }

        // Décrémenter le stock
        $product->setStock($product->getStock() - $quantity);

        // Trouver tous les items de l'utilisateur
        $items = $itemsRepository->findBy(['user' => $user]);

        $found = false;
        foreach ($items as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                // Déjà présent : incrémenter
                $item->setQuantity($item->getQuantity() + $quantity);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $item = new Items();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $item->setUser($user);
            $entityManager->persist($item);
        }

        $entityManager->flush();

        return new JsonResponse(['success' => 'Product added to cart']);
    }




    #[Route('/api/removeItem/{id}', name: 'app_remove_item', methods: ['DELETE'])]
    public function removeItem(
        int $id,
        ItemsRepository $itemsRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not found'], 401);
        }

        $item = $itemsRepository->find($id);

        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        $entityManager->remove($item);
        $product = $item->getProduct();
        $product->setStock($product->getStock() + $item->getQuantity());
        $entityManager->flush();

        return $this->json(['success' => 'Item removed']);
    }

}
