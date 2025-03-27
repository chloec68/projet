<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\VATpriceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AuthentifiedUserController extends AbstractController
{
   // USER PROFILE
   // USER PROFILE > VIEW BILLS 
   #[Route('/profile/bills', name:'user_bills')]
   #[Route('/profile', name: 'app_user')]
   public function userProfile(Security $security, KernelInterface $kernel):Response
   {    
        //récupère l'utilisateur en session pour pouvoir récupérer ses infos dans la vue 
        $user = $security->getUser();

        //VOIR LES FACTURES 
        //récupère l'utilisateur en session 
        $userId = $security->getUser()->getId();
        //construction du chemin du fichier 
        $billsDirectory = $kernel->getProjectDir() . '/public/bills/';
        //Retourne un tableau de fichier et dossier, issus de directory. 
        $bills = scandir($billsDirectory); 
        //Filtre les fichiers pour récupérer les factures de l'utilisateur 
        $userBills = array_filter($bills,function($file) use ($userId){
            return strpos($file, '_user' . $userId) !== false && pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
        });
        // si factures non trouvées
        if (empty($userBills)) {
            throw $this->createNotFoundException('No bills found for this user.');
        }
 
       return $this->render('home/profile.html.twig', [
           'user' => $user,
           'userBills' => $userBills,
           'meta_description' => 'Bienvenue sur votre profil utilisateur'
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
   public function showFavorites(Security $security, VATpriceCalculator $priceCalculator)
   {
        $user = $security->getUser();
        $vatPricesArray=[];
       if($user){
           $favorites = $user->getFavoriteProducts();
           foreach($favorites as $favorite){
            $vatPrice = $priceCalculator->vatPrice($favorite);
            $vatPricesArray[$favorite->getId()]=$vatPrice;
           }
       }

       return $this->render('home/favorites.html.twig', [
           'favorites' => $favorites,
           'vatPricesArray'=> $vatPricesArray,
           'meta_description' => 'Mes produits favoris'
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


// USER PROFILE > VIEW BILL 
   #[Route('/profile/bills')]
    public function viewBill(Security $security, KernelInterface $kernel):Response
    {   
        //récupère l'utilisateur en session 
        $userId = $security->getUser()->getId();
        //construction du chemin du fichier 
        $filePath = $kernel->getProjectDir() . '/public/bills/bill_' . '_user' . $userId .'.pdf';
        //si le fichier n'est pas trouvé 
        if (!file_exists($filePath)){
            throw $this->createNotFoundException('File not found');
        }

        //retourne le fichier au navigateur 
        // return new Response(file_get_contents($filePath),200,[
        //     'Content-Type' => 'application/pdf', //informe le navigateur que le contenu est un fichier PDF
        //     'Content-Disposition' => 'inline; filename="facture_sainte-cru"', //suggère un nom pour quand l'utilisateur décide de le télécharger
        // ]);
        
    }

}
