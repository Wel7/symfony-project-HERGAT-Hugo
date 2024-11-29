<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VtuberRepository;

class VtuberController extends AbstractController
{
    #[Route('/vtuber', name: 'app_vtuber',)]
    public function index(VtuberRepository $vr): Response
    {

        $vtubers = $vr->findAll();

        return $this->render('vtuber/index.html.twig', [
            'vtubers' => $vtubers,
        ]);
    }
}
