<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error/404', name: 'custom_404')]
    public function show(): Response
    {
        return $this->render('error/error.html.twig', [], new Response('', 404));
    }
}
