<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use App\Form\IdentificationFormType;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Component\Clock\now;






class PaymentController extends AbstractController
{

    #[Route('/payment/identification', name:'app_payment-identification')]
    public function identification(Request $request, SessionInterface $session, Security $security, ProductRepository $productRepository): Response
    {   
        // je crée un nouvel objet commande 
        $order = new Order();
        // je crée le formulaire associé à la classe commande delaquelle j'ai instancié l'objet
        $identificationForm = $this->createForm(IdentificationFormType::class, $order);
        // je traite la requête http envoyée par le client après soumission du formulaire
        $identificationForm->handleRequest($request); 
        // si le formulaire a été soumis et que les champs sont valides 
        if ($identificationForm->isSubmitted() && $identificationForm->isValid()) {
            // je récupère les data du formulaire 
            $data = $identificationForm->getData();
            //j'ajoute la date et l'heure à la commande
            $now = now();
            $order->setDateOfPlacement($now);
            //j'ajoute le prénom à la commande
            $userFirstName = $data->getOrderUserFirstName();
            $order->setOrderUserFirstName($userFirstName);
            //j'ajoute le nom à la commande 
            $userLastName = $data->getOrderUserLastName();
            $order->setOrderUserLastName($userLastName);
            //j'ajoute l'email à la commande
            $userEmail = $data->getOrderEmail();
            $order->setOrderEmail($userEmail);
            //j'ajoute le panier à la commande 
            $cart = $session->get('cart');
            // dd($cart);
            foreach ($cart as $productId => $quantity) {
                $product = $productRepository->find($productId);
                if($product){
                    $order->addProduct($product,$quantity);
                }
            }
            // s'il existe en session, j'ajoute l'objet user
            $user = $security->getUser();
            if($user){
                $order->setAppUser($user);
            }

            dd($order);

            
            
            //je récupère le panier 
            $cart = $session->get('cart',[]);

                
            // $order->setReference(); // générer une référence
            // $order->setDateOfPlacement(); // générer la date "now"
            // products -> récupérer les produits en session
            //orderIsCollected 
            // orderTotal -> récupérer le total en session 
            // appUser -> récupérer l'utilisateur en session s'il existe, sinon null 

                

        // j'organise les produits en session sous forme de tableau 
        foreach($cart as $product => $quantity){
            $cartData[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        // PB : récupérer le total + les sous-totaux ? 


        // PB: toutes les infos sont dans l'URL

     

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
        
   
