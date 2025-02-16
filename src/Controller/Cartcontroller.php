<?php 
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CartController extends AbstractController 
{   

    #[Route('/cart', name:'app_cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $cart = $session->get('cart',[]);

        $data=[];
        $total=0;
        $subtotal=0;
        $nbItems=0;
  
        foreach($cart as $id=>$quantity){
            $product = $productRepository->find($id);

            if ($product !== null){
                $data[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal = $product->getProductPrice() * $quantity
                ];
                $total += $product->getProductPrice() * $quantity;
                $nbItems += $quantity ; 
            }
        }

        return $this->render('cart/index.html.twig',[       
            'data'=>$data,

            'total'=>$total,
            'subtotal'=>$subtotal,
            'nbItems'=>$nbItems
            ]);
    }

    #[Route('cart/add/{id}', name:'app_cart-add')]
    public function add(Product $product, SessionInterface $session)
    {   
        //on récupère l'id du produit 
        $id = $product->getId();

        //je récupère le panier existant s'il y en a un - si panier n'existe pas en session, on en crée un (tableau vide) ;
        $cart = $session->get('cart',[]);

        //on ajoute le produit dans le panier s'il n'y est pas encore - sinon on incrémente la qté
        if(empty($cart[$id])){
            $cart[$id] =1;
        }else{
            $cart[$id]++; 
        }
       
       //j'ajoute le panier à la session en lui attribuant la clé (le nom) 'panier'
       $session->set('cart',$cart);

    //    dd($session);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('cart/empty', name:'app_cart-empty')]
    public function emptyCart(SessionInterface $session)
    {
        $cart = $session->get('cart',[]); 
        if($cart){
            $session->unset('cart',$cart);
        }else{
            $message = "Le panier est déjà vide";
        }

        return $this->render('cart/index.html.twig',[
            "message" => $message
        ]);
    }

    #[Route('cart/remove/{id}', name:'app_cart-remove')]
    public function removeProduct(SessionInterface $session,Product $product)
    {
        $cart = $session->get('cart',[]);
        $id = $product->getId();

        if(!empty($cart[$id])){
            if($cart[$id] > 1){
                $cart[$id]--;
            }else{
                unset($cart[$id]);
            }
        }

        $session->set('cart',$cart);

        return $this->redirectToRoute('app_cart');
    }

    // ***********************************************************************************************************

    // ADD ITEM FROM BASKET 

    #[Route('/cart/addAjax', name:'app_cart-addAjax')]
    public function addAjax(Request $request, SessionInterface $session)
    {
        //json_decode() => fonction native PHP qui récupère une chaîne encodée JSON et la convertit en valeur PHP
        // paramètre 'true' => les objets JSON sont retournés comme tableaux associatifs
            // {
            //     "productId":1,
            //     "quantity":2
            // }
            // devient 
            // ["productId" => 1,
            // "quantity" => 2]
        // paramètre 'false' => les objets JSON sont retournés comme des objets 
        $data = json_decode($request->getContent(),true);

        $product = $data['product'];
        $quantity = $data['quantity']; 
        $cart = $session->get('cart',[]);

        if(empty($cart[$product])){
            $cart[$product] = $quantity;
        }else{
            $cart[$product] += $quantity; 
        }

        $session->set('cart',$cart);

        $nbItems = array_sum($cart); // array_sum() => native PHP function that returns the sum of values in an array

        return new JsonResponse(['nbItems'=>$nbItems]);
    }

    // REMOVE ITEM FROM BASKET 

    #[Route('/cart/removeAjax', name:'app_cart-removeAjax')]
    public function removeAjax(Request $request, SessionInterface $session)
    { 
        $data = json_decode($request->getContent(),true);

        $product = $data['product'];
        $quantity = $data['quantity']; 
        $cart = $session->get('cart',[]);

        if(isset($cart[$product])){
            if($cart[$product] >1){
                $cart[$product] --;
            }else{
                unset ($cart[$product]); 
            }
            $session->set('cart',$cart);
            $nbItems = array_sum($cart);

            return new JsonResponse(['nbItems'=>$nbItems]);
        }else{
            return new JsonResponse(['error' => 'Produit non trouvé dans le panier'], 400);
        }
    }
}
