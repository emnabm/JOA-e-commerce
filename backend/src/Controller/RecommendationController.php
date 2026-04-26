<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use App\Service\RecommendationService;
use App\Entity\ProductView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

#[Route('/api/recommendations')]
class RecommendationController extends AbstractController
{
    public function __construct(
        private ProductsRepository $productRepo,
        private RecommendationService $recService,
        private EntityManagerInterface $em
    ) {}

    #[Route('/track-view', name: 'rec_track_view', methods: ['POST'])]
    public function trackView(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['error' => 'Login required'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $productId = (int)($data['productId'] ?? 0);

        $product = $this->productRepo->find($productId);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        try {
            $view = new ProductView();
            $view->setUser($user);
            $view->setProduct($product);
            $this->em->persist($view);
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['success' => true]);
    }

    // ✅ Route publique - pas d'authentification requise
    #[Route('/history', name: 'rec_history', methods: ['POST'])]
    public function getHistoryRecommendations(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $historyIds = $data['ids'] ?? [];
            $currentId = (int)($data['currentId'] ?? 0);

            $products = $this->recService->getHistoryBasedRecommendations($historyIds, $currentId);

            return $this->json($this->serializeProducts($products));
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/purchases', name: 'rec_purchases', methods: ['POST'])]
    public function getPurchaseRecommendations(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $currentId = (int)($data['currentId'] ?? 0);

            $products = $this->recService->getPurchaseBasedRecommendations($user, $currentId);

            return $this->json($this->serializeProducts($products));
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/similar/{productId}', name: 'rec_similar', methods: ['GET'])]
    public function getSimilarProducts(int $productId): JsonResponse
    {
        try {
            $products = $this->recService->getSimilarProducts($productId);
            return $this->json($this->serializeProducts($products));
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/trending', name: 'rec_trending', methods: ['GET'])]
    public function getTrending(): JsonResponse
    {
        try {
            $products = $this->recService->getTrendingProducts();
            return $this->json($this->serializeProducts($products));
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function serializeProducts(array $products): array
    {
        return array_map(function($p) {
            return [
                'id' => $p->getId(),
                'nom' => $p->getNom(),
                'prix' => $p->getPrix(),
                'image' => $p->getImage(),
                'description' => $p->getDescription(),
                'stock' => $p->getStock(),
                'unit' => $p->getUnit(),
                'avgRating' => $p->getAvgRating(),
                'reviewCount' => $p->getReviewCount(),
                'category' => $p->getCategory()?->getName(),
            ];
        }, $products);
    }
}