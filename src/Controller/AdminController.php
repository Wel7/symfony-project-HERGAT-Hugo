<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\OrderRepository;

class AdminController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    // App\Controller\ArticleController.php

    #[Route('/admin/product', name: 'app_admin_product')]
    public function listActionProduct(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        $manualCounting = 7377;
        $query = $em->createQuery("SELECT p FROM App\Entity\Product p")
        ->setHint('knp_paginator.count', $manualCounting);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', default: 1), /* page number */
            24 /* limit per page */
        );

        // parameters to template
        return $this->render('admin/product.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/admin/orders', name: 'app_admin_order')]
    public function listActionOrder(OrderRepository $or, PaginatorInterface $paginator, Request $request)
    {
        $query=$or->findAllCompleteQuery();

        $pagination = $or->findAllCompleteQuery();

        // parameters to template
        return $this->render('admin/orders.html.twig', ['pagination' => $pagination]);
    }

    
}
