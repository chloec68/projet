<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session; 

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
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




