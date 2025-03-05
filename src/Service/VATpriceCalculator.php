<?php 

namespace App\Service;

use App\Entity\Product;

class VATpriceCalculator{

    public function vatPrice(Product $product): float
    {
        $result = $product->getProductPrice() * (1 + $product->getVAT()->getVATRate());

        return number_format($result,2,'.','');
    }

    public function vatPriceSubTotal(Product $product, int $quantity) : float 
    { 
        $price = $product->getProductPrice() ; 
        $vatRate = $product->getVat()->getVatRate();
        $vat = $price * $vatRate ; 
        $vatPrice = $price + $vat ; 
        $subTotal = $vatPrice * $quantity;

        return number_format($subTotal,2,'.',' ') ;
    }
}


