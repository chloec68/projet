<?php 
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/cart', name:'cart_')]
class CartController extends AbstractController 
{   

    #[Route('/', name:'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $cart = $session->get('cart',[]);

        $data=[];
        $total=0;

        foreach($cart as $id=>$quantity){
            $product = $productRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getProductPrice() * $quantity;
        }

        // dd($data);
        // dd($total);

        return $this->render('cart/index.html.twig',[       
            'data'=>$data,
            compact('data'),
            'total'=>$total
            ]);
    }

    #[Route('/add/{id}', name:'add')]
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
}



//renderView
//#[template]
//renderBlock
//renderBlockView


