<?php 

namespace App\Service;

use App\Entity\Product;

class VATpriceCalculator{

    public function vatPrice(Product $product): float
    {
        $result = $product->getProductPrice() * (1 + $product->getVAT()->getVATRate());

        return number_format($result,2,'.','');
    }
}

