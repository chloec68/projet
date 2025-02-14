<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {   

        $permanent = $productRepository->findByPermanency(true,1);

        $ephemeral = $productRepository->findByPermanency(false,1);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'permanent' => $permanent,
            'ephemeral' => $ephemeral
        ]);
    }

}
