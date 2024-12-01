<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategorieRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_category', requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    public function categories(CategorieRepository $cr): Response
    {
        $categories = $cr->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
