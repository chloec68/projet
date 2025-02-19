<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use App\Form\IdentificationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;





class PaymentController extends AbstractController
{

    #[Route('/payment/identification', name:'app_identification')]
    public function idenfication(Request $request): Response
    {   
        $order = new Order();
        $identificationForm = $this->createForm(IdentificationFormType::class, $order);
        // $identificationForm->handleRequest($request);

        // if ($identificationForm->isSubmitted() && $identificationForm->isValid()) {}

        return $this->render('/payment/identification.html.twig',[
            'controller_name' => 'PaymentController',
            'identificationForm' => $identificationForm,
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
        
   
