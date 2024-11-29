<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vtuber;

class ProductController extends AbstractController
{
    #[Route('/product/{name}', name: 'app_product')]
    public function index($name, EntityManagerInterface $em): Response
    {
        $transformedName = explode('-', $name);
        $transformedName = implode(' ', $transformedName);

        $query = $em->getRepository(Product::class)->queryOneProductForProduct($transformedName);
        dump($transformedName);
        dump($query);
        $result = $query->getResult();

        if (!$result) {
            throw $this->createNotFoundException('The product does not exist.');
        }

        $result->getCategories()->initialize();   


        return $this->render('product/index.html.twig', [
            'product' => $result[0],
        ]);
    }

    #[Route('/collection/{name}-{id}', name: 'app_collection',  requirements: ['name' => '[a-zA-Z0-9\-_+().]+', 'id' => '\d+'])]
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
    
        // Récupérer les produits associés
        $name = $vtuber->getName();
        $products = $vtuber->getProducts();
        $products->initialize();
    
        // Passer les données au template
        return $this->render('product/collection.html.twig', [
            'name' => $name,
            'products' => $products,
        ]);
    }
}
