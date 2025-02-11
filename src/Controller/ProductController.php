<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchForm;
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

    #[Route('/product/beers', name: 'app_beers')]
    #[Route('/product/beers/search', name:'app_beers_search')]
    public function allBeers(ProductRepository $productRepository): Response
    {   

        $data = new Product();
        $form = $this->createForm(SearchForm::class,$data);

        $products = $productRepository->findBeers([],['productName'=>'ASC']);
        return $this->render('product/beers.html.twig', [
            'products'=>$products,
            'form'=>$form
        ]);
    }

    // DISPLAY ALL BEERS + BEERS BY TYPE + BEERS BY COLOR + BEER BY NAME (SEARCH BAR)
    // #[Route('/product/beers', name: 'app_beers')]
    // #[Route('/product/beers/type', name:'app_beers-by-type')]
    // #[Route('/product/beers/color', name:'app_beers-by-color')]
    // #[Route('/product/beers/search', name:'app_beers_search')]
    // public function findBeers(ProductRepository $productRepository, Request $request):Response
    // {   
 
    //     $type = $request->query->get('type');
    //     $color = $request->query->get('color');
    //     $name = $request->query->get('name');
    //     $products="";
    //     $product="";

    //     if($type){
    //         $products = $productRepository->findBeersByType($type);
    //     }else if($color){
    //         $products = $productRepository->findBeersByColor($color);
    //     }else if($name){
    //         $product = $productRepository->findOneByName($name);
    //     }else{
    //         $products = $productRepository->findBeers([],['productName'=>'ASC']);
    //     }
    //     return $this->render('product/beers.html.twig',[
    //         'products'=>$products,
    //         'type'=>$type,
    //         'product'=>$product
    //         // 'color'=>$color
    //     ]);
    // }

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
