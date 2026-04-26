<?php

namespace App\Controller;
use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/messages')]
#[IsGranted('ROLE_ADMIN')]
class MessageController extends AbstractController
{
    /**
     * GET - Récupérer tous les messages
     */
    #[Route('', name: 'admin_get_messages', methods: ['GET'])]
    public function getMessages(MessageRepository $messageRepository): JsonResponse
    {
        $messages = $messageRepository->findAll();
        
        // Trier par date décroissante (plus récent en premier)
        usort($messages, function($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->getId(),
                'firstName' => $message->getFirstName(),
                'lastName' => $message->getLastName(),
                'name' => $message->getFirstName() . ' ' . $message->getLastName(),
                'text' => $message->getText(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'email' => $message->getEmail(),
                'isRead' => $message->isRead(),
                'status' => $message->isRead() ? 'read' : 'unread',
            ];
        }

        return $this->json($result);
    }

    /**
     * GET - Récupérer un message spécifique
     */
    #[Route('/{id}', name: 'admin_get_message', methods: ['GET'])]
    public function getMessage(int $id, MessageRepository $messageRepository): JsonResponse
    {
        $message = $messageRepository->find($id);
        
        if (!$message) {
            return $this->json(['error' => 'Message not found'], 404);
        }

        // Marquer comme lu si ce ne l'est pas déjà
        if (!$message->isRead()) {
            $message->setIsRead(true);
            $messageRepository->save($message, true);
        }

        return $this->json([
            'id' => $message->getId(),
            'firstName' => $message->getFirstName(),
            'lastName' => $message->getLastName(),
            'name' => $message->getFirstName() . ' ' . $message->getLastName(),
            'text' => $message->getText(),
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            'email' => $message->getEmail(),
            'isRead' => $message->isRead(),
        ]);
    }

    /**
     * PATCH - Marquer un message comme lu
     */
    #[Route('/{id}/read', name: 'admin_mark_read', methods: ['PATCH'])]
    public function markAsRead(int $id, MessageRepository $messageRepository): JsonResponse
    {
        $message = $messageRepository->find($id);
        
        if (!$message) {
            return $this->json(['error' => 'Message not found'], 404);
        }

        $message->setIsRead(true);
        $messageRepository->save($message, true);

        return $this->json([
            'success' => true, 
            'message' => 'Message marked as read'
        ]);
    }

    /**
     * PATCH - Marquer plusieurs messages comme lus
     */
    #[Route('/bulk/read', name: 'admin_bulk_mark_read', methods: ['POST'])]
    public function bulkMarkAsRead(Request $request, MessageRepository $messageRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            return $this->json(['error' => 'No message IDs provided'], 400);
        }

        $messages = $messageRepository->findBy(['id' => $ids]);
        $count = 0;
        
        foreach ($messages as $message) {
            if (!$message->isRead()) {
                $message->setIsRead(true);
                $count++;
            }
        }
        
        $messageRepository->save($messages, true);

        return $this->json([
            'success' => true,
            'message' => $count . ' messages marked as read'
        ]);
    }

    /**
     * DELETE - Supprimer un message
     */
    #[Route('/{id}', name: 'admin_delete_message', methods: ['DELETE'])]
    public function deleteMessage(int $id, MessageRepository $messageRepository): JsonResponse
    {
        $message = $messageRepository->find($id);
        
        if (!$message) {
            return $this->json(['error' => 'Message not found'], 404);
        }

        $messageRepository->remove($message, true);

        return $this->json([
            'success' => true, 
            'message' => 'Message deleted successfully'
        ]);
    }

    /**
     * POST - Supprimer plusieurs messages (bulk delete)
     */
    #[Route('/bulk/delete', name: 'admin_bulk_delete_messages', methods: ['POST'])]
    public function bulkDeleteMessages(Request $request, MessageRepository $messageRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            return $this->json(['error' => 'No message IDs provided'], 400);
        }

        $messages = $messageRepository->findBy(['id' => $ids]);
        
        foreach ($messages as $message) {
            $messageRepository->remove($message, false);
        }
        
        $messageRepository->flush();

        return $this->json([
            'success' => true,
            'message' => count($messages) . ' messages deleted successfully'
        ]);
    }

    /**
     * GET - Statistiques des messages
     */
    #[Route('/stats', name: 'admin_messages_stats', methods: ['GET'])]
    public function getMessagesStats(MessageRepository $messageRepository): JsonResponse
    {
        $messages = $messageRepository->findAll();
        
        $total = count($messages);
        $unread = 0;
        $read = 0;
        
        foreach ($messages as $message) {
            if ($message->isRead()) {
                $read++;
            } else {
                $unread++;
            }
        }
        
        return $this->json([
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
        ]);
    }
}
