<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchFormBeers;
use App\Model\SearchDataBeers;
use App\Form\SearchFormGoodies;
use App\Model\SearchDataGoodies;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
   
    // DISPLAY ALL GOODIES + SEARCH
    #[Route('/product/goodies', name: 'app_goodies')]
    public function allGoodies(ProductRepository $productRepository, Request $request): Response
    {   
        $products = $productRepository->findGoodies([],['productName'=>'ASC']);
        $searchData = new SearchDataGoodies();
        $form = $this->createForm(SearchFormGoodies::class,$searchData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $searchData->setCategory(2);
            $products = $productRepository->searchProductGoodie($searchData);
        }
        return $this->render('product/goodies.html.twig', [
            'products'=>$products,
            'form'=>$form
        ]);
    }

    
    // DISPLAY ALL BEERS + SEARCH
    #[Route('/product/beers', name: 'app_beers')]
    #[Route('/product/beers/search', name:'app_beers_search')]
    public function allBeers(ProductRepository $productRepository, Request $request): Response
    {   
        // Default query for all products (beers)
        $products = $productRepository->findBeers([],['productName'=>'ASC']);
        $searchData = new SearchDataBeers();
        $form = $this->createForm(SearchFormBeers::class,$searchData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $searchData->setCategory(1);
            $products = $productRepository->searchProductBeer($searchData);
        }

        return $this->render('product/beers.html.twig', [
            'products'=>$products,
            'form'=>$form
        ]);
    }


    // DISPLAY A SPECIFIC PRODUCT
    #[Route('/product/{id}/detail', name: 'detail_product')]
    public function detailProduct(Product $product, ProductRepository $productRepository):Response
    {   
 
        return $this->render('/product/detail-product.html.twig',[
            'product'=>$product
        ]);
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
