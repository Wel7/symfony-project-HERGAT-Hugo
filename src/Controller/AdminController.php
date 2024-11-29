<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Form\ProductFormType;
use App\Entity\Product;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(OrderRepository $or, ProductRepository $pr): Response
    {
    
        //For orders stats
    $currentDate = (new \DateTime())->sub(\DateInterval::createFromDateString('1 year'));
    
    $interval = \DateInterval::createFromDateString('1 month');

    $last12Months = [];
    for ($i = 11; $i >= 0; $i--) {
        $last12Months[] = $currentDate->add($interval)->format('Y-m');
        -$i;
    }

    $ordersData = $or->getOrdersCountByMonth();
    $formatData = [];
    foreach ($ordersData as $order) {
        $formatData[$order['month']] = [$order['ordersCount'], $order['totalRevenue']];
    }
    
    $months = $last12Months;
    $ordersCount = [];
    $ordersTotal = [];

    foreach ($months as $month) {
        $ordersCount[] = isset($formatData[$month]) ? $formatData[$month][0] : 0;
        $ordersTotal[]= isset($formatData[$month]) ? $formatData[$month][1] : 0;
    }

        //For item stats
        $availabilityStatusCounts = $pr->getProductAvailabilityCounts();

        $statuses = ['Disponible', 'Rupture', 'Précommande']; // Example statuses, adjust as needed
        $counts = [
            'disponible' => $availabilityStatusCounts['disponible'] ?? 0,
            'rupture' => $availabilityStatusCounts['rupture'] ?? 0,
            'precommande' => $availabilityStatusCounts['precommande'] ?? 0,
        ];

        //for the order
        $lastOrders = $or->findLastFiveOrders();

    return $this->render('admin/admin.html.twig', [
        'months' => $months,
        'ordersCount' => $ordersCount,
        'ordersTotal' => $ordersTotal,
        'statuses' => $statuses,
        'counts' => $counts,
        'lastOrders' => $lastOrders,
    ]);

    }

    #[Route('/admin/product', name: 'app_admin_products')]
    public function listActionProduct(PaginatorInterface $paginator, Request $request)
    {
        $manualCounting = 7377;
        $query = $this->entityManager->createQuery("SELECT p FROM App\Entity\Product p");
        //->setHint('knp_paginator.count', $manualCounting);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', default: 1), /* page number */
            24 /* limit per page */
        );

        return $this->render('admin/product.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function listActionOrder(OrderRepository $or)
    {
        $pagination = $or->findAllCompleteQuery();
        return $this->render('admin/orders.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/admin/addProduct', name: 'app_admin_add_product')]
    public function adminAddProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);

        foreach ($product->getImage() as $image) {
            $image->setProduct($product);
            $this->entityManager->persist($image);
        }
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('product/addProduct.html.twig', ['form' => $form]);
    }

    #[Route('/admin/deleteProduct/{id}', name: 'app_admin_delete_product')]
    public function deleteProduct(int $id, Request $request)
    {

        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('The product does not exist.');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $route = $request->headers->get('referer');

        return $this->redirect($route);    
    }




    
}
