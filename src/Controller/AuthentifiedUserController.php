<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\VATpriceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AuthentifiedUserController extends AbstractController
{
   // USER PROFILE
   #[Route('/profile', name: 'app_user')]
   public function userProfile(Security $security):Response
   {   
       $user = $security->getUser();
 
       return $this->render('home/profile.html.twig', [
           'user' => $user,
           'meta_description' => ''
       ]);
   }

   // USER PROFILE > ORDER 
   #[Route('/profile/order/{id}', name:'app_order')]
   public function showOrder(OrderRepository $orderRepository, int $id, VATpriceCalculator $VATpriceCalculator)
   {   
       $order = $orderRepository->find($id);
       $orderProducts = $order->getOrderProducts();

       $nbItems = 0;
       $subTotals = [];
       foreach($orderProducts as $orderProduct){
           $quantity = $orderProduct->getQuantity();
           $product = $orderProduct->getAppProduct() ;
           $subTotal = $VATpriceCalculator->vatPriceSubTotal($product,$quantity);
           $subTotals[$orderProduct->getAppProduct()->getId()] = $subTotal; 

           $nbItems += $quantity;
       }
       return $this->render('home/order.html.twig', [
           'order' => $order,
           'subTotals' =>$subTotals,
           'nbItems' => $nbItems,
           'meta_description' => 'Le détail de votre commande'
       ]);
   }

   // USER PROFILE > FAVORITES 
   #[Route('/profile/favorites', name:'app_favorites')]
   public function showFavorites(Security $security)
   {
        $user = $security->getUser();
       if($user){
           $favorites = $user->getFavoriteProducts();
       }

       return $this->render('home/favorites.html.twig', [
           'favorites' => $favorites
       ]);
   }

   //USER PROFILE > DELETE ACCOUNT
   #[Route('/profile/delete-account',name:'delete-account')]
   public function deleteAccount(Security $security,EntityManagerInterface $entityManager, Session $session ): JsonResponse
   {
        $user = $security->getUser();
        if(!$user){
            return newJsonResponse(['success'=>false]);
        }
        try{
            $email = $user->getEmail();
            $hashedEmail = hash('sha256',$email);
            $password = $user->getPassword();
            $rehashedPassword = password_hash(bin2hex(random_bytes(16)),PASSWORD_DEFAULT);
            $user->setEmail($hashedEmail);
            $user->setRoles(["ROLE_DELETED"]);
            $entityManager->persist($user);
            $entityManager->flush();
            $session = new Session();
            $session->invalidate();

            return new JsonResponse(['success' => true]);

        }catch (\Exception $e){
            return newJsonResponse(['success'=>false]);
        }   
   }
}


// JsonResponse::HTTP_UNAUTHORIZED
// JsonResponse::HTTP_INTERNAL_SERVER_ERROR