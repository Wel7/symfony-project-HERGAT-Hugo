<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vtuber;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\AddToCartType;
use Psr\Log\LoggerInterface;

class ProductController extends AbstractController
{
    #[Route('/product/{name}', name: 'app_product')]
    public function index($name, EntityManagerInterface $em, Request $request, LoggerInterface $logger): Response
    {
        //Show items
        $transformedName = explode('-', $name);
        $transformedName = implode(' ', $transformedName);

        $query = $em->getRepository(Product::class)->queryOneProductForProduct($transformedName);
        $result = $query->getResult();

        if (!$result) {
            throw $this->createNotFoundException('The product does not exist.');
        }

        $result[0]->getCategories()->initialize();

        $productStock = $result[0]->getStock();
        $productId = (string) $result[0]->getId();
        $productPrice = (float) $result[0]->getPrice();
        $mercureUrl = $this->getParameter('mercurePublicUrl');


        //addToCart
        $form = $this->createForm(AddToCartType::class, null, [
            'productStock' =>  $productStock,
            'productId' =>  $productId,
            'productPrice' =>  $productPrice,
            'attr' => ['data-turbo' => 'true'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $logger->info("C'est invalide parceque ????");
            foreach ($form->getErrors(true) as $error) {
                $logger->info('Error: ' . $error->getMessage());
            }
            $logger->info('Form data: ' . json_encode($form->getData()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $logger->info("test3");
            $item = $form->getData();
            dump($item);
            $user = $this->getUser();

            $order = $em->getRepository(Order::class)->findOneBy([
                'user' => $user,
                'status' => OrderStatus::CART,
            ]);



            $orderItem = new OrderItem();
            $orderItem->setProduct($result[0]);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setProductPrice($item['productPrice']);
            $em->persist($orderItem);

            if (!$order) {
                $order = new Order();
                $order->setUser($user);
                $order->setReference(uniqid());
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setStatus(OrderStatus::CART);
            }
            $order->addOrderItem($orderItem);
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('app_cart');
        }


        return $this->render('product/index.html.twig', [
            'product' => $result[0],
            'form' => $form->createView(),
            'mercureHubUrl' => $mercureUrl,
        ]);
    }

    #[Route('/collection/{name}-{id}', name: 'app_collection',  requirements: ['name' => '[a-zA-Z0-9\-_+().&✨\']+', 'id' => '\d+'])]
    public function collection($name, $id, EntityManagerInterface $em): Response
    {

        $formatedName = str_replace('_', ' ', $name);

        $vtuber = $em->getRepository(Vtuber::class)->findOneBy([
            'id' => $id,
            'name' => $formatedName,
        ]);

        if (!$vtuber) {
            throw $this->createNotFoundException("Vtuber with ID $id and name $formatedName not found.");
        }

        $name = $vtuber->getName();
        $products = $vtuber->getProducts();
        $products->initialize();

        return $this->render('product/collection.html.twig', [
            'name' => $name,
            'products' => $products,
        ]);
    }

    #[Route('/category/{name}-{id}', name: 'app_category',  requirements: ['name' => '[a-zA-Z0-9\-_+().&✨\']+', 'id' => '\d+'])]
    public function category($name, $id, EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {

        $formatedName = str_replace('_', ' ', $name);

        $query = $em->getRepository(Product::class)->findProductsByCategoryId($id);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', default: 1), /* page number */
            24 /* limit per page */
        );


        return $this->render('product/category.html.twig', ['pagination' => $pagination, 'name' => $formatedName,]);
    }
}
