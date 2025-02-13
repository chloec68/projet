<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    
    private $product;

    public function __construct(SessionInterface $session, ProductRepository $product)
    {
        $this->session = $session;

        $this->product = $product;
    }

    public function displayCart()
    {
            $cart = $this->$session->get('cart',[]);
    
            $data=[];
            $total=0;
            $subtotal=0;
            $nbItems=0;
      
            foreach($cart as $id=>$quantity){
                $this->product = $productRepository->find($id);
                
                $data[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal = $product->getProductPrice() * $quantity
                ];
                $total += $product->getProductPrice() * $quantity;
                $nbItems += $quantity ; 
            }
    
            // dd($data);
            // dd($total);
            // dd($cart);
    
            return $cart;
    }

    public function addToCart($id)
    {
      
        //je récupère le panier existant s'il y en a un - si panier n'existe pas en session, on en crée un (tableau vide) ;
         $cart = $this->$session->get('cart',[]);
 
         //on ajoute le produit dans le panier s'il n'y est pas encore - sinon on incrémente la qté
         if(empty($cart[$id])){
             $cart[$id] =1;
         }else{
             $cart[$id]++; 
         }
        //j'ajoute le panier à la session en lui attribuant la clé (le nom) 'panier'
        $this->$session->set('cart',$cart);
 
     //    dd($session);
    }
}




