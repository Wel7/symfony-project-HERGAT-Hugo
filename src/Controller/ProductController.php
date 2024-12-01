<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vtuber;
use App\Entity\Categorie;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


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

        $result[0]->getCategories()->initialize();


        return $this->render('product/index.html.twig', [
            'product' => $result[0],
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

        dump($pagination);

        return $this->render('product/category.html.twig', ['pagination' => $pagination, 'name' => $formatedName,]);
    }
}
