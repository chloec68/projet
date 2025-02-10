<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    // DISPLAY ALL BEERS 
    // #[Route('/product/beers', name: 'app_beers')]
    // public function allBeers(ProductRepository $productRepository): Response
    // {   
    //     $products = $productRepository->findBeers([],['productName'=>'ASC']);
    //     return $this->render('product/beers.html.twig', [
    //         'products'=>$products
    //     ]);
    // }

    // DISPLAY ALL GOODIES
    #[Route('/product/goodies', name: 'app_goodies')]
    public function allGoodies(ProductRepository $productRepository): Response
    {   
        $products = $productRepository->findGoodies([],['productName'=>'ASC']);
        return $this->render('product/goodies.html.twig', [
            'products'=>$products
        ]);
    }

    // DISPLAY BEERS BY TYPE 
    #[Route('/product/beers', name: 'app_beers')]
    #[Route('/product/beers/type', name:'app_beers-by-type')]
    public function beersByType(ProductRepository $productRepository, Request $request):Response
    {   
        $type = $request->query->get('type');
        if($type){
            $products = $productRepository->findBeersByType($type);
        }else{
            $products = $productRepository->findBeers([],['productName'=>'ASC']);
        }
        return $this->render('product/beers.html.twig',[
            'products'=>$products,
            'type'=>$type
        ]);
    }


    // DISPLAY A SPECIFIC PRODUCT
    #[Route('/product/{id}/display', name: 'display_product')]
    public function displayProduct(Product $product, EntityManagerInterface $entityManager):Response
    {

    }

    // ADD A PRODUCT (ADMIN)
    #[Route('/product/new', name:'new_product')]
    public function newProduct():Response
    {

    }

    //DELETE A PRODUCT FROM APP (GENERAL SCOPE) (ADMIN)
    #[Route('/product/{id}/delete', name:'delete_product')]
    public function deleteProduct(Product $product):Response
    {

    }

    //UPDATE A PRODUCT (ADMIN)
    #[Route('product/{id}/update', name:'update_product')]
    public function updateProduct(Product $product)
    {

    }
}
