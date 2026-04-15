<?php

namespace App\Controller;


use App\Entity\Products;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/api/me', name:'api_me', methods:['GET','POST'])]
    public function me(): JsonResponse
    {
        $user =$this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'phone' => $user->getPhone(),
            'address' => $user->getAdress(),
        ]);
    }

    #[Route('/api/getInfos', name:'api_infos', methods:['GET'])]
    public function getInfos(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        //compter les commandes de l'utilisateur :

        $orders = $user->getOrders();
        $totalOrders = count($orders);

        $totalSpent = 0;
        $lastOrderStatus = null;
        $lastOrderDate = null;

        foreach ($orders as $order) {
            if($order->getStatus()==='delivered') {
                $totalSpent += $order->getTotal();}

            if (!$lastOrderDate || $order->getDate() > $lastOrderDate) {
                $lastOrderDate = $order->getDate();
                $lastOrderStatus = $order->getStatus();
            }
        }

        return $this->json([
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent,
            'lastOrderStatus' => $lastOrderStatus,
            'memberSince' => $user->getCreateAt()->format('Y-m-d'),
        ]);
    }

    #[Route('/api/updateProfile', name:'api_update_user', methods:['PUT'])]
    public function updateUser(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse{
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        $data=json_decode($request->getContent());
        $user->setUsername($data->username);
        $user->setEmail($data->email);
        $user->setPhone($data->phone);
        $user->setAdress($data->adress);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json('success');
    }

    #[Route('/api/change-password', name: 'api_change_password', methods: ['PUT'])]
    public function changePassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Récupère l'utilisateur actuellement authentifié (ex: via JWT)
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $oldPassword = $data['oldPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;

        // Vérifie que tout est saisit
        if (!$oldPassword || !$newPassword) {
            return new JsonResponse(['error' => 'Missing parameters'], 400);
        }

        // Vérifie l'ancien mot de passe
        if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
            return new JsonResponse(['error' => 'Old password is incorrect'], 400);
        }

        // Encode le nouveau mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);


        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Password changed successfully']);
    }




}
