<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchFormBeers;
use App\Model\SearchDataBeers;
use App\Form\SearchFormGoodies;
use App\Model\SearchDataGoodies;
use App\Service\VATpriceCalculator;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
   
    // DISPLAY ALL GOODIES + SEARCH
    #[Route('/product/goodies', name: 'app_goodies')]
    public function allGoodies(ProductRepository $productRepository, Request $request, VATpriceCalculator $VATpriceCalculator, Security $security): Response
    {   
        $idCategory = 2;
        $isDeleted = false;
        $products = $productRepository->findGoodies($idCategory,$isDeleted,[],['productName'=>'ASC']);

        foreach ($products as $product) {
            $VATprice = number_format($VATpriceCalculator->VATprice($product),2,'.','');
            $product->setProductVATprice($VATprice);
        }

        $searchData = new SearchDataGoodies();
        $form = $this->createForm(SearchFormGoodies::class,$searchData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $searchData->setCategory(2);
            $products = $productRepository->searchProductGoodie($searchData);
        }

        $user = $security->getUser();
        $favorites = "";
        if($user){
            $favorites = $user->getFavoriteProducts();
        }

        return $this->render('product/goodies.html.twig', [
            'products'=>$products,
            'form'=>$form,
            'favorites'=>$favorites,
            'meta_description'=>'Merchandising'
        ]);
    }

    
    // DISPLAY ALL BEERS + SEARCH 
    #[Route('/product/beers', name: 'app_beers')]
    #[Route('/product/beers/search', name:'app_beers_search')]
    public function allBeers(ProductRepository $productRepository, Request $request,VATpriceCalculator $VATpriceCalculator, Security $security): Response
    {   
        $idCategory=1;
        $isDeleted = false;
        $products = $productRepository->findBeers($idCategory,$isDeleted,[],['productName'=>'ASC']);

        foreach ($products as $product) {
            $VATprice = number_format($VATpriceCalculator->VATprice($product),2,'.','');
            $product->setProductVATprice($VATprice);
        }

        $searchData = new SearchDataBeers();
        $form = $this->createForm(SearchFormBeers::class,$searchData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $searchData->setCategory(1);
            $products = $productRepository->searchProductBeer($searchData);
        }

        $favorites="";
        $user = $security->getUser();
        if($user){
            $favorites = $user->getFavoriteProducts();
        }

        return $this->render('product/beers.html.twig', [
            'products'=>$products,
            'form'=>$form,
            'favorites'=>$favorites,
            'meta_description'=>'Nos bières'
        ]);
    }

    // DISPLAY A SPECIFIC PRODUCT
    #[Route('/product/{id}/detail', name: 'detail_product')]
    public function detailProduct(Product $product, ProductRepository $productRepository,VATpriceCalculator $VATpriceCalculator):Response
    {   
        $VATprice = number_format($VATpriceCalculator->VATprice($product),2,'.','');
        $product->setProductVATprice($VATprice);
        return $this->render('/product/detail-product.html.twig',[
            'product'=>$product,
            'VATprice' =>$VATprice,
            'meta_description'=>$product->getProductName()
        ]);
    }

    //ADD TO FAVORITES 
    #[Route('/favorite/add/{productId}',name:'add-to-favorites')]
    public function addFavorite(ProductRepository $productRepository,Security $security, int $productId,EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $productRepository->find($productId);
        $user = $security->getUser();
        $product->addUser($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['success' => true,'productId'=>$productId]);
    }

    //REMOVE FROM FAVORITES
    #[Route('/favorite/remove/{productId}', name:'remove-from-favorites')]
    public function removeFromFavorites(Security $security, int $productId, ProductRepository $productRepository,EntityManagerInterface $entityManager){
        $user = $security->getUser();
        $product = $productRepository->find($productId);
        if($user && $product){
            $favorites = $user->getFavoriteProducts();
            if($favorites->contains($product)){
                $user->removeProduct($product);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        return new JsonResponse(['success'=>true,'productId'=>$productId]);
        }
    }

}
