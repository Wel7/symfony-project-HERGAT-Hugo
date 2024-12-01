<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OrderItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


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
                'status' => $order->getStatus()->value,
                'user' => $order->getUser(),
                'orderItems' => $order->getOrderItems()->toArray()
            ];
        } else {
            $orderData = null;
        }

        $mercureUrl = $this->getParameter('mercurePublicUrl');

        return $this->render('order/index.html.twig', [
            'orderData' => $orderData,
            'mercureHubUrl' => $mercureUrl,
        ]);
    }

    #[Route('/order/{id}', name: 'product_buy', methods: ['POST'])]
    public function buyOrder(Order $order, EntityManagerInterface $entityManager, HubInterface $hub): JsonResponse
    {
        if ($order->getStatus() !== OrderStatus::CART) {
            return new JsonResponse(['error' => 'Commande invalide ou déjà en préparation'], 400);
        }

        $products = $order->getOrderItems();
        foreach ($products as $product) {
            $product = $product->getProduct();
            if ($product->getStock() <= 0) {



                return new JsonResponse([
                    'error' => sprintf('Le produit "%s" est en rupture de stock', $product->getName())
                ], 400);
            }
            $product->setStock($product->getStock() - 1);
            if ($product->getStock() == 0) {

                $orderItemsToRemove = $entityManager->getRepository(OrderItem::class)->createQueryBuilder('oi')
                    ->innerJoin(Order::class, 'o', 'WITH', 'oi.orderI = o.id')
                    ->andWhere('oi.product = :product')
                    ->andWhere('o.id != :currentOrderId')
                    ->setParameter('product', $product)
                    ->setParameter('currentOrderId', $order->getId())
                    ->getQuery()->getResult();

                foreach ($orderItemsToRemove as $orderItem) {
                    $entityManager->remove($orderItem);
                }
                $entityManager->flush();
                $updateOrderItem = new Update(
                    '/order/' . $order->getId(), // Le topic
                    json_encode([
                        'message' => 'Le produit a été supprimé des autres commandes',
                        'productId' => $product->getId()
                    ])
                );

                $hub->publish($updateOrderItem);
            }
        }

        $order->setStatus(OrderStatus::PREPARATION);

        $entityManager->flush();

        foreach ($products as $product) {
            $product = $product->getProduct();
            $updateProduct = new Update(
                sprintf('/products/%d/stock', $product->getId()),
                json_encode(['id' => $product->getId(), 'stock' => $product->getStock()])
            );
            $hub->publish($updateProduct);
        }




        return new JsonResponse(['success' => true, 'orderStatus' => OrderStatus::PREPARATION,]);
    }
}
