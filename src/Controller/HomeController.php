<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\VATpriceCalculator;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
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

        return $this->render('home/index.html.twig', [
            'permanent' => $permanent,
            'ephemeral' => $ephemeral,
            'meta_description' => 'Bienvenue sur le site de la Brasserie locale et artisanale Sainte Cru sur laquelle vous pourrez découvrir nos produits et les acheter directement en ligne'
        ]);
    }

    // USER PROFILE
    #[Route('/user', name: 'app_user')]
    public function userProfile(Security $security):Response
    {   
        $user = $security->getUser();
  
        return $this->render('home/profile.html.twig', [
            'user' => $user,
            'meta_description' => ''
        ]);
    }

    // USER PROFILE > ORDER 
    #[Route('/user/order/{id}', name:'app_order')]
    public function showOrder(OrderRepository $orderRepository, int $id, VATpriceCalculator $VATpriceCalculator)
    {   
     
        $order = $orderRepository->find($id);

        $orderProducts = $order->getOrderProducts();


        $subTotals = [];
        foreach($orderProducts as $orderProduct){
            $quantity = $orderProduct->getQuantity();
            $product = $orderProduct->getAppProduct() ;
            $subTotal = $VATpriceCalculator->vatPriceSubTotal($product,$quantity);

            $subTotals[$orderProduct->getAppProduct()->getId()] = $subTotal; 
        }

        // $subTotals = [];
        // foreach($orderProducts as $orderProduct){
        //     $quantity = $orderProduct->getQuantity();
        //     $price = $orderProduct->getAppProduct()->getProductPrice();
        //     $vatRate = $orderProduct->getAppProduct()->getVat()->getVatRate();
        //     $vat = $price * $vatRate ;
        //     $vatPrice = $price + $vat;
        //     $subTotal = $vatPrice * $quantity;
        //     $subTotals[$orderProduct->getAppProduct()->getId()] = $subTotal; 

        //     //$orderProduct->getSubTotal()
        // }
        
        return $this->render('home/order.html.twig', [
            'order' => $order,
            'subTotals' =>$subTotals,
            'meta_description' => 'Le détail de votre commande'
        ]);
    }
}
