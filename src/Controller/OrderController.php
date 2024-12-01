<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $order = $em->getRepository(Order::class)->findOneBy([
            'user' => $user,
            'status' => OrderStatus::CART,
        ]);

        if ($order) {
            $orderData = [
                'id' => $order->getId(),
                'reference' => $order->getReference(),
                'createdAt' => $order->getCreatedAt(),
                'status' => $order->getStatus()->value, // Convertir l'enum en chaîne
                'user' => $order->getUser(),
                'orderItems' => $order->getOrderItems()->toArray() // Convertir la collection en tableau
            ];
        } else {
            $orderData = null;
        }

        return $this->render('order/index.html.twig', [
            'orderData' => $orderData,
        ]);
    }
}
