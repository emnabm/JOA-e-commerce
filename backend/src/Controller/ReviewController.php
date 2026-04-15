<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Review;
use App\Repository\ProductsRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/api/review/{id}', name: 'app_review_show', methods: ['GET'])]
    public function getReview(
        int $id,
        ReviewRepository $reviewRepository,
        ProductsRepository $productsRepository
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse("User not found", Response::HTTP_UNAUTHORIZED);
        }

        $product = $productsRepository->find($id);
        if (!$product) {
            return new JsonResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        $reviews = $reviewRepository->findBy(['product' => $product]);

        if (!$reviews) {
            return new JsonResponse("No reviews found", 201);
        }

        $result = [];
        foreach ($reviews as $review) {
            $result[] = [
                'id' => $review->getId(),
                'text' => $review->getText(),
                'date' => $review->getDate()?->format('Y-m-d'),
                'user' => $review->getUser()->getUsername(),
                'rating' => $review->getRating(),
            ];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }


    #[Route('/api/review', name: 'api_review', methods: ['POST'])]
    public function addReview(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $productId = $data['productId'] ?? null;
        $text = $data['text'] ?? '';
        $rating = $data['rating'] ?? 0;

        if (!$productId || !$text) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $product = $em->getRepository(Products::class)->find($productId);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $review = new Review();
        $review->setProduct($product);
        $review->setUser($user);
        $review->setText($text);
        $review->setDate(new \DateTime());
        $review->setRating($rating);

        $em->persist($review);
        $em->flush();

        return new JsonResponse(['success' => true], 201);
    }
    #[Route('/topRate', name: 'api_review_top', methods: ['Get'])]
    public function topRate(ReviewRepository $reviewRepository): JsonResponse
    {
        $topProductRate=$reviewRepository->findTopRatedProducts(4);
        return new JsonResponse($topProductRate, Response::HTTP_OK);
    }
}
