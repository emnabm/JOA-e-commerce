<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category', methods: ['GET'])]
    #[Route('/admin/categories', name: 'app_categories', methods: ['GET'])]
    public function get_category(CategoryRepository $categoryRepository): JsonResponse
    {   $category = $categoryRepository->findAll();
        return $this->json($category, 200, [], ['groups' => 'category:read']);
    }


    #[Route('admin/categories', name: 'admin_categories_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($data['name'] ?? '');
        $category->setImg($data['img'] ?? '');

        $em->persist($category);
        $em->flush();

        return $this->json([
            'message' => 'Category created',
            'id' => $category->getId(),
        ]);
    }

    #[Route('admin/categories/{id}', name: 'admin_categories_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, CategoryRepository $repository): JsonResponse
    {
        $category = $repository->find($id);

        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $category->setName($data['name'] ?? $category->getName());
        $category->setImg($data['img'] ?? $category->getImg());

        $em->flush();

        return $this->json(['message' => 'Category updated']);
    }

    #[Route('admin/categories/{id}', name: 'admin_categories_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, CategoryRepository $repository): JsonResponse
    {
        $category = $repository->find($id);

        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'Category deleted']);
    }

}
