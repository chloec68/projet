<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use App\Form\IdentificationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;





class PaymentController extends AbstractController
{

    #[Route('/payment/identification', name:'app_payment-identification')]
    public function identification(Request $request, SessionInterface $session): Response
    {   
        // je crée une nouvelle commande 
        $order = new Order();
        // je récupère les produits en session 
        // $cart = $session->get('cart',[]);
        // j'organise les produits en session sous forme de tableau 
        // foreach($cart as $product => $quantity){
        //     $cartData[] = [
        //         'product' => $product,
        //         'quantity' => $quantity,
        //     ];
        // }
        // dd($cartData);
        // PB : récupérer le total + les sous-totaux ? 

        // $user = $session->get('user');

        $identificationForm = $this->createForm(IdentificationFormType::class, $order);
        // $identificationForm->handleRequest($request); 

        // PB: toutes les infos sont dans l'URL

        if ($identificationForm->isSubmitted() && $identificationForm->isValid()) {
            $data = $identificationForm->getData();
            dd($data);
            // $order->setOrderUserFirstName($data);
            // $order->setOrderUserLastName($data);
            // $order->setOrderEmail($data);
            // $order->setReference(); // générer une référence
            // $order->setDateOfPlacement(); // générer la date "now"
            // products -> récupérer les produits en session
            //orderIsCollected -> vérifier que set à false 
            // orderTotal -> récupérer le total en session 
            // appUser -> récupérer l'utilisateur en session s'il existe, sinon null 

            return $this->redirectToRoute('app_payment-collection');
        }

        return $this->render('/payment/identification.html.twig',[
            'controller_name' => 'PaymentController',
            'identificationForm' => $identificationForm,
        ]);
    }
   
    #[Route('/payment/collection', name:'app_payment-collection')]
    public function collection(Request $request): Response
    {
        return $this->render('/payment/collection.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }






    
    #[Route('/charge/cart/{id}', name:'charge')]
    public function charge(Request $request, ProductRepository $productRepository,): Response{

        $stripeSecretKey = 'sk_test_51QuA8b2M19Cf3LVfWJz7Rfz26oC27Xt5ZkaYQW1sYqSf0QKgVl5sdPkOLUZUpYtVWLs4GtX6BE0QkSNEtxJo7bFJ00jaZoqcwT';
        
        Stripe::setApiKey($stripeSecretKey);
        Stripe::setApiVersion('2025-01-27.acacia');

        $cart = $session->get('cart',[]);

        foreach($cart as $id=>$quantity){
            $product = $productRepository->find($id);

            if ($product !== null){
                $data[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        // header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $checkout_session = Session::create([
            'line_items' => [
               array_map(fn (array $product) => [
                    'quantity' => $quantity,
                    'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $cart['data']['product']['productName']
                        ],
                    'unit_amount' => $cart['data']['product']['productPrice']
                    ]
                ], $session->get('cart',[]))
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html.twig',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html.twig',
            ]);
    
        }

    }
        
   
