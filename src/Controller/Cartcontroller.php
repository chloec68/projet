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
        $quantity = 0;
  
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
            'nbItems'=>$nbItems,
            'quantity'=>$quantity
            ]);
    }

       // ADD ITEM TO CART  

       #[Route('/cart/update/{id}', name:'app_cart-add')]
       public function add(Request $request, SessionInterface $session)
       {
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
   
           return new JsonResponse(['success' => true,'nbItems'=>$nbItems,'cart' => $cart]);
       }

    // REMOVE AN ITEM FROM CART 
    #[Route('cart/remove/{id}', name:'app_cart-remove')]
    public function removeProduct(SessionInterface $session,Product $product, Request $request)
    {   
        $data = json_decode($request->getContent(),true);
        $cart = $session->get('cart',[]);
        $id = $product->getId();

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }
        
    $session->set('cart',$cart);
    $nbItems = array_sum($cart);
        
    return new JsonResponse(['success' => true, 'nbItems'=>$nbItems]);
    }

}
