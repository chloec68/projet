<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository, SessionInterface $session): Response
    {   

        $permanent = $productRepository->findByPermanency(true,1);

        $ephemeral = $productRepository->findByPermanency(false,1);

        // $cart = $session->get('cart',[]);

        // $nbItems = array_sum($cart);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'permanent' => $permanent,
            'ephemeral' => $ephemeral,
            'meta_description' => 'Bienvenue sur le site de la Brasserie locale et artisanale Sainte Cru sur laquelle vous pourrez découvrir nos produits et les acheter directement en ligne'
            // 'nbItems' => $nbItems
        ]);
    }

}
