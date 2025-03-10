<?php 
namespace App\Controller;

use App\Entity\Product;
use App\Service\VATpriceCalculator;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CartController extends AbstractController 
{   

    #[Route('/cart', name:'app_cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository, VATpriceCalculator $VATpriceCalculator): Response
    {
        $cart = $session->get('cart',[]);
        $data=[];
        $total=0;
        $formattedTotal = 0;
        $formattedSubtotal = 0;
        $subtotal=0;
        $nbItems=0;
        $quantity = 0;

        foreach($cart as $id=>$quantity){
            $product = $productRepository->find($id);
            if ($product !== null){
                $VATprice = $VATpriceCalculator->VATprice($product);
                $product->setProductVATprice($VATprice);
                $pictures = [];
                foreach ($product->getPictures() as $picture){
                    $pictures[] = $picture->getPictureName();
                }
                
                $dataItem = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'VATprice' => $VATprice,
                    'subtotal' => number_format(floatval($product->getProductVATprice()) * $quantity,2,'.',''),
                    'pictures' => $pictures,
                ];

                $type = $product->getType();
                if(!empty($type)){
                    $dataItem['typeName']=  $type->getTypeName();
                }

                $data[] = $dataItem;

                $total += floatval($product->getProductVATprice()) * $quantity;
                $formattedTotal = number_format($total, 2, '.', '');
                $nbItems += $quantity ; 
            }
        }
        $session->set('cartData',$data); // je set les data en session pour les rendre accessible partout dans l'application et pas seulement dans la vue du panier
        $session->set('priceTotal',$formattedTotal);
        $session->set('nbItems',$nbItems);

        return $this->render('cart/index.html.twig',[       
            'data'=>$data,
            'total'=>$formattedTotal,
            'subtotal'=>$formattedSubtotal,
            'nbItems'=>$nbItems,
            'quantity'=>$quantity,
            'meta_description' => 'Votre panier contient ' . $nbItems . ' articles. Vous pouvez continuer vos achats ou choisir votre point de retrait et procéder au paiement.'
            ]);
    }

       // ADD ITEM TO CART  

       #[Route('/cart/add/{id}', name:'app_cart-add')]
       public function add(Request $request, SessionInterface $session): JsonResponse
       {
           $data = json_decode($request->getContent(),true);
   
           $product = $data['product'];
           $quantity = $data['quantity']; 
           $size = $data['size'];
           $cart = $session->get('cart',[]);

   
           if(empty($cart[$product])){
               $cart[$product] = $quantity;
           }else{
               $cart[$product] += $quantity; 
           }
           
           $session->set('cart',$cart);
           $nbItems = array_sum($cart); // array_sum() => native PHP function that returns the sum of VALUES in an array
   
           return new JsonResponse(['success' => true,'nbItems'=>$nbItems,'cart' => $cart]);
       }

    // REMOVE AN ITEM FROM CART 
    #[Route('cart/remove/{id}', name:'app_cart-remove')]
    public function removeProduct(SessionInterface $session,Product $product, Request $request): JsonResponse
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


    #[Route('cart/side-cart', name:'app_side-cart')]
    public function retrieveCart(SessionInterface $session, ProductRepository $productRepository, VATpriceCalculator $VATpriceCalculator): JsonResponse
    {
        $cart = $session->get('cart');
        $data = [];
        $nbItems = 0;
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if($product){
                $VATprice = number_format($VATpriceCalculator->VATprice($product),2,'.','');
                $product->setProductVATprice($VATprice);
                $total += $VATprice;
                $nbItems += $quantity;
                $pictures = [];
                foreach ($product->getPictures() as $picture){
                    $pictures[] = $picture->getPictureName();

                    if(isset($picture)){
                        $picture = $pictures[0];
                    }else{
                        $picture = "";
                    }
                }

                $size = $product->getSize();
                if (isset($size)){
                    $size = $size->getSizeName();
                }else{
                    $size="";
                }

                $type = $product->getType();
                if(isset($type)){
                    $type = $type->getTypeName();
                }else{
                    $type ="";
                }

                $volume = $product->getProductVolume();
                if(isset($volume)){
                    $volume;
                }else{
                    $volume="";
                }

                $data[]=[
                    'productId' => $product->getId(),
                    'productName'=>$product->getProductName(),
                    'VATprice'=> $VATprice,
                    'picture' => $picture,
                    'quantity'=>$quantity,
                    'type' => $type,
                    'color' => $product->getProductColor(),
                    'volume' => $volume,
                    'size' => $size,
                    'total' => $total,
                    'nbItems' => $nbItems
                ];
            }
        }
        return $this->json($data);
    }

}
