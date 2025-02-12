<?php 
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CartController extends AbstractController 
{   

    #[Route('/cart', name:'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $cart = $session->get('cart',[]);

        $data=[];
        $total=0;
        $subtotal=0;
        $nbItems=0;
  

        foreach($cart as $id=>$quantity){
            $product = $productRepository->find($id);
            
            $data[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal = $product->getProductPrice() * $quantity
            ];
            $total += $product->getProductPrice() * $quantity;
            $nbItems += $quantity ; 
        }

        // dd($data);
        // dd($total);
        // dd($cart);

        return $this->render('cart/index.html.twig',[       
            'data'=>$data,
            compact('data'),
            'total'=>$total,
            'subtotal'=>$subtotal,
            'nbItems'=>$nbItems
            ]);
    }

    #[Route('cart/add/{id}', name:'add')]
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

       return $this->render('cart/index.html.twig',[       
       'cart'=>$cart,
       'id'=>$id
       ]);
    }

    // #[Route('/cart/qty-up/{id}')]
    // public function upQuantity(){

    // }
}



//renderView
//#[template]
//renderBlock
//renderBlockView


