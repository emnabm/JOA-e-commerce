<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ItemsRepository;
use App\Repository\OrderRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class OrderController extends AbstractController
{
    #[Route('/api/createOrder', name: 'api_create_order', methods: ['POST'])]
    public function createOrder(
        Request $request,
        EntityManagerInterface $em,
        ItemsRepository $itemsRepository,
        MailerInterface $mailer
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $address = $data['address'] ?? null;
        $delivery = $data['delivery'] ?? null;

        if (!$address || !$delivery) {
            return $this->json(['error' => 'Address and delivery required'], 400);
        }

        $cart = $itemsRepository->findBy(['user' => $user]);

        if ($cart == null) {
            return $this->json(['error' => 'Your cart is empty'], 400);
        }

        $order = new Order();
        $order->setUser($user);
        $order->setDate(new \DateTime());
        $order->setStatus('pending');
        $order->setAdressOrder($address);
        $order->setDelivery($delivery);

        $totalPrice = 0;

        foreach ($cart as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setOrderId($order);

            $order->addOrderItem($orderItem);
            $em->persist($orderItem);

            // Calcul du total
            $totalPrice += $cartItem->getProduct()->getPrix() * $cartItem->getQuantity();
        }

        $order->setTotal($totalPrice);

        // Vider le panier
        foreach ($cart as $item) {
            $em->remove($item);
        }

        $em->persist($order);
        $em->flush();

//        // Envoyer l'email de confirmation
//        $emailMessage = (new Email())
//            ->from('no-reply@monsite.com')
//            ->to($user->getEmail())
//            ->subject('Order Confirmation')
//            ->html("
//                <h1>Thank you for your order!</h1>
//                <p>Your order #{$order->getId()} has been placed successfully.</p>
//                <p><strong>Address:</strong> {$order->getAdressOrder()}</p>
//                <p><strong>Delivery Method:</strong> {$order->getDelivery()}</p>
//                <p><strong>Total:</strong> {$order->getTotal()} dt</p>
//                <p>We will keep you updated by email.</p>
//            ");
//
//        $mailer->send($emailMessage);

        return $this->json([
            'message' => 'Order created and confirmation sent.',
            'orderId' => $order->getId(),
            'total' => $order->getTotal()
        ]);
    }


    #[Route('/api/my_orders', name: 'api_get_order', methods: ['GET'])]
    public function getOrders(OrderRepository $orderRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User Not Found'], 401);
        }

        $orders = $orderRepository->findBy(['user' => $user]);

        $result = [];

        foreach ($orders as $order) {
            $result[] = [
                'id' => $order->getId(),
                'date' => $order->getDate()->format('Y-m-d'),
                'status' => $order->getStatus(),
                'address' => $order->getAdressOrder(),
                'delivery' => $order->getDelivery(),
                'price' => $order->getTotal()
            ];
        }

        return $this->json($result);
    }

    #[Route('/admin/updateStatus/{id}', name: 'api_update_order', methods: ['put'])]
    public function updateOrder(
        int $id,
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $em,
    ):JsonResponse{
        $order=$orderRepository->findOneBy(['id'=>$id]);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }
        $data = json_decode($request->getContent(), true);

        $order->setStatus($data['status']);

        $em->persist($order);
        $em->flush();

        return $this->json('Success');

    }



}
