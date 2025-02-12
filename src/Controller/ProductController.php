<?php

namespace App\Controller;

use App\Entity\Product;
use App\Model\SearchData;
use App\Form\SearchForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
   
    // DISPLAY ALL GOODIES
    #[Route('/product/goodies', name: 'app_goodies')]
    public function allGoodies(ProductRepository $productRepository): Response
    {   
        $products = $productRepository->findGoodies([],['productName'=>'ASC']);
        return $this->render('product/goodies.html.twig', [
            'products'=>$products
        ]);
    }

    
    // DISPLAY ALL BEERS + SEARCH
    #[Route('/product/beers', name: 'app_beers')]
    #[Route('/product/beers/search', name:'app_beers_search')]
    public function allBeers(ProductRepository $productRepository, Request $request): Response
    {   
        // Default query for all products (beers)
        $products = $productRepository->findBeers([],['productName'=>'ASC']);

        $searchData = new SearchData();
        $form = $this->createForm(SearchForm::class,$searchData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $products = $productRepository->searchProduct($searchData);
        }

        return $this->render('product/beers.html.twig', [
            'products'=>$products,
            'form'=>$form
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
