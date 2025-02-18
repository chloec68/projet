<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AppExtension extends AbstractExtension
{
    public function getGlobals(SessionInterface $session): array
    {
        $cart = get('cart',[]);

        $nbItems = sum_array($cart);
        return [
            'nbItems' => $nbItems,  
        ];
    }
}