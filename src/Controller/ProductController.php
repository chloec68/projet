<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    // DISPLAY ALL PRODUCTS 
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    // DISPLAY A SPECIFIC PRODUCT
    #[Route('/product/{id}/display', name: 'display_product')]
    public function displayProduct(Product $product, EntityManagerInterface $entityManager):Response
    {

    }

    // ADD A PRODUCT 
    #[Route('/product/new', name:'new_product')]
    public function newProduct():Response
    {

    }

    //DELETE A PRODUCT FROM APP (GENERAL SCOPE)
    #[Route('/product/{id}/delete', name:'delete_product')]
    public function deleteProduct(Product $product):Response
    {

    }

    //UPDATE A PRODUCT 
    #[Route('product/{id}/update', name:'update_product')]
    public function updateProduct(Product $product)
    {
        
    }
}
