<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ChatController extends AbstractController
{
    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';

        // Génère une réponse simple
        $responseText = "Désolé, je ne comprends pas.";
        if (stripos($message, 'bonjour') !== false) {
            $responseText = "Bonjour 👋 !";
        }

        // Sauvegarde le message utilisateur
        $msgUser = new Message();
        $msgUser->setSender('user');
        $msgUser->setText($message);
        $msgUser->setCreatedAt(new \DateTimeImmutable());
        $em->persist($msgUser);

        // Sauvegarde la réponse bot
        $msgBot = new Message();
        $msgBot->setSender('bot');
        $msgBot->setText($responseText);
        $msgBot->setCreatedAt(new \DateTimeImmutable());
        $em->persist($msgBot);

        $em->flush();

        return new JsonResponse(['response' => $responseText]);
    }
}
