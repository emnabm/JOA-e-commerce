<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(UsersRepository $usersRepository): JsonResponse
    {   $users=$usersRepository->findAll();
        return $this->json($users);
    }

    #[Route('/users/add', name: 'app_user_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new Users();
        $user->setFirstName($data['firstName'] ?? '');
        $user->setLastName($data['lastName'] ?? '');
        $user->setEmail($data['email'] ?? '');
        $user->setPhoneNumber($data['phoneNumber'] ?? '');
        $user->setAdress($data['adress'] ?? '');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['success' => true, 'id' => $user->getId()]);
    }

    #[Route('/users/{id}/edit', name: 'app_user_edit')]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer l'utilisateur par son id
        $user = $entityManager->getRepository(Users::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        // Récupérer les données envoyées (ex: JSON)
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['phoneNumber'])) {
            $user->setPhoneNumber($data['phoneNumber']);
        }
        if (isset($data['adress'])) {
            $user->setAdress($data['adress']);
        }

        // Persister les changements
        $entityManager->flush();

        return $this->json(['success' => true, 'user' => [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName()
        ]]);
    }

    #[Route('/users/{id}/delete', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/users/{id}', name: 'app_user')]
    public function oneUser(int $id,UsersRepository $usersRepository): JsonResponse
    {   $user=$usersRepository->findOneBy(['id'=>$id]);
        if ($user) {
            return $this->json($user);}
        return new JsonResponse(['message'=>'User not found'],404);

    }

}
