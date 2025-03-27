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
        //AFFICHAGE DES PRODUITS 
        $idCategory = 2;
        $isDeleted = false;
        $products = $productRepository->findGoodies($idCategory,$isDeleted,[],['productName'=>'ASC']);
        //calcul prix TTC (pour l'affichage)
        foreach ($products as $product) {
            $VATprice = number_format($VATpriceCalculator->VATprice($product),2,'.','');
            $product->setProductVATprice($VATprice);
        }
        //FILTRE DE RECHERCHE 
        $searchData = new SearchDataGoodies(); //création d'une instance de la classe SearchDataBeers 
                                                //cette instance va contenir les critères de recherche 
        $form = $this->createForm(SearchFormGoodies::class,$searchData); //création d'un formulaire à partir de la classe SearchFormBeers
                                                                        //association du formulaire à l'objet searchData créé précédemment
        $form->handleRequest($request); //traitement de la requête HTTP => associe les données de la requête au formulaire

        if($form->isSubmitted() && $form->isValid()){ //si le formulaire est soumis et est valide
            $searchData->setCategory(2); //définition de la catégorie de produits concernés par la recherche 
                                        //en base de données, la catégorie "goodies" a l'identifiant 2
            $products = $productRepository->searchProductGoodie($searchData);//appelle de la méthode du repository pour lui passer les données de recherche
        }
        //FAVORIS 
        $user = $security->getUser(); // récupération de l'utilisateur en session
        $favorites = "";    // initialisation de la variables $favorites 
        if($user){  // si $user est définie
            $favorites = $user->getFavoriteProducts(); //récupére les produits favoris de l'utilisateur 
        }
        //rend la vue et passe les paramètres nécessaires, c'est à dire le formulaire 
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
