<?php

namespace App\Controller;


use App\Entity\Items;
use App\Entity\Products;
use App\Repository\CategoryRepository;
use App\Repository\ProductsRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProductsController extends AbstractController
{
    #[Route('/admin/addProduct', name: 'app_add_product', methods: ['POST'])]
    public function addProduct(
        Request $request,
        ProductsRepository $productsRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager,
    ):JsonResponse {
        $data = json_decode($request->getContent(), true);

        $product = new Products();
        $product->setNom($data['nom']);
        $product->setDescription($data['description']);
        $product->setPrix($data['prix']);
        $product->setImage($data['image']);
        $product->setStock($data['stock']);
        $product->setCategory($categoryRepository->find($data['category']));
        $product->setUnit($data['unit']);
        $entityManager->persist($product);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Product added successfully'], 201);
    }

    #[Route('/admin/products', name: 'app_get_products', methods: ['GET'])]
    public function get_products(ProductsRepository $productsRepository): JsonResponse{
        $products = $productsRepository->findAll();
        return $this->json($products, 200, [], ['groups' => ['product:read']]);
    }


    #[Route('/getProduct_{id}', name: 'app_product', methods: ['GET'])]
    public function getProduct(
        int $id,
        ProductsRepository $productsRepository,
        ReviewRepository $reviewRepository
    ): JsonResponse {
        $product = $productsRepository->find($id); // find() pas findBy() car id est unique

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $avg = $reviewRepository->getAverageRating($id);
        $count = $reviewRepository->count(['product' => $product]);

        return $this->json(
            [
                'product' => $product,
                'avgRating' => $avg,
                'reviewCount' => $count
            ],
            200,
            [],
            ['groups' => ['products:read', 'category:read']]
        );
    }

    #[Route('/products_{id}', name: 'app_products', methods: ['GET'])]
    public function getProducts(int $id,ProductsRepository $productsRepository):JsonResponse
    {
        $products= $productsRepository->findBy(['category'=>$id]);
        return $this->json($products, 200, [], ['groups' => ['products:read','category:read']]);
    }

    #[Route('admin/products', name: 'admin_products_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Products();
        $product->setNom($data['nom'] ?? '');
        $product->setDescription($data['description'] ?? '');
        $product->setPrix($data['prix'] ?? 0);
        $product->setStock($data['stock'] ?? 0);
        $product->setUnit($data['unit'] ?? '');

        $em->persist($product);
        $em->flush();

        return $this->json([
            'message' => 'Product created',
            'id' => $product->getId(),
        ]);
    }

    #[Route('admin/products/{id}', name: 'admin_products_update', methods: ['PUT'])]
    public function update(int $id, Request $request, ProductsRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $product = $repository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $product->setNom($data['nom'] ?? $product->getNom());
        $product->setDescription($data['description'] ?? $product->getDescription());
        $product->setPrix($data['prix'] ?? $product->getPrix());
        $product->setStock($data['stock'] ?? $product->getStock());
        $product->setUnit($data['unit'] ?? $product->getUnit());

        $em->flush();

        return $this->json(['message' => 'Product updated']);
    }

    #[Route('admin/products/{id}', name: 'admin_products_delete', methods: ['DELETE'])]
    public function delete(int $id, ProductsRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $product = $repository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted']);
    }

    #[Route('/products/suggestions/{id}', name: 'product_suggestions', methods: ['GET'])]
    public function suggestions(int $id, ProductsRepository $repo): JsonResponse
    {

        $product = $repo->find($id);

        if (!$product) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }

        $suggestions = $repo->findSuggestions($product);

        return $this->json($suggestions, 200, [], ['groups' => 'product:read']);
    }


}
