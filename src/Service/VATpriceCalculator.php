<?php 

namespace App\Service;

use App\Entity\Product;

class VATpriceCalculator{

    public function VATprice(Product $product): float
    {
     
        return $product->getProductPrice() * (1 + $product->getVAT()->getVATRate());
    }
}

