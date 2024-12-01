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

class HomeController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {}

    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {

        $query = $em->getRepository(Product::class)->findProductsByVtuberId(91);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', default: 1), /* page number */
            24 /* limit per page */
        );

        dump($query);

        return $this->render('home.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'pagination' => $pagination,
        ]);
    }
}
