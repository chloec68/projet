<?php 

namespace App\Service;

use App\Entity\Product;

class VATpriceCalculator{

    public function vatPrice(Product $product): float
    {   
         // récupère le prix de base (H.T.) du produit et le multiplie par (1 + taux de TVA)
        $vatPrice= $product->getProductPrice() * (1 + $product->getVAT()->getVATRate());
        // retourne le prix T.T.C., formaté avec deux décimales (pour l'affichage)
        return number_format($vatPrice,2,'.','');
    }

    public function vatPriceSubTotal(Product $product, int $quantity) : float 
    {   
        $vatPrice= $product->getProductPrice() * (1 + $product->getVAT()->getVATRate());

        $subTotal = $vatPrice * $quantity;

        return number_format($subTotal,2,'.',' ') ;
    }
}


        // $price = $product->getProductPrice() ; 
      
        // $vatRate = $product->getVat()->getVatRate();
     
        // $vat = $price * $vatRate ; 
    
        // $vatPrice = $price + $vat ; 

        // $subTotal = $vatPrice * $quantity;

        // return number_format($subTotal,2,'.',' ') ;