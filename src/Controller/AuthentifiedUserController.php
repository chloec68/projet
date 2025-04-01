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
        //récupère les commandes de l'utilisateur
        $userOrders = $user->getOrders(); 
        //si la variable $userOrders n'est pas falsy
        if(!empty($userOrders)){
            // j'initialise un tableau vide 
            $userBills = [];
            //pour chaque commande 
            foreach($userOrders as $userOrder){
                //je récupère la facture
                $bill = $userOrder->getBill();
                // si la facture n'est pas nulle et que le chemin de la facture n'est pas nul
                if(!empty($bill) && $bill->getBillpath() !== null){
                         // j'ajoute chaque facture au tableau 
                $userBills[] = $bill;
                }
            }
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
        //récupère l'utilisateur en session 
        $user = $security->getUser();
        //initialisation d'un tableau vide 
        $vatPricesArray=[];
        //si un user est enregistré en session 
       if(!empty($user)){
            // récupère les favoris de l'utilisateur 
           $favorites = $user->getFavoriteProducts();
           // pour chaque favori 
           foreach($favorites as $favorite){
            // calcul du prix TTC via le service dédié
            $vatPrice = $priceCalculator->vatPrice($favorite);
            // ajout du prix TTC au tableau associatif ayant pour clé l'identifiant du favori concerné 
            // et pour valeur son prix TTC
            $vatPricesArray[$favorite->getId()] = $vatPrice;
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
   public function deleteAccount(Security $security,EntityManagerInterface $entityManager, Session $session ): Response
   {
        $user = $security->getUser();
        if(empty($user)){ // si aucun utilisateur n'est trouvé en session 
            $this->addFlash('error', 'Utilisateur non trouvé.'); // message d'erreur 
            return $this->redirectToRoute('app_home'); // redirection vers page d'accueil
        }
        try{
            $email = $user->getEmail(); // je récupère l'email de l'utilisateur 
            $salt = bin2hex(random_bytes(16)); // je génère un sel pour éviter d'obtenir un hache identique
            $hashedEmail = hash('sha256',$email . $salt); // je hache l'adresse email 
            $user->setEmail($hashedEmail); // je remplace l'adresse email par l'adresse hachée et donc anonymisée
            $newPassword = password_hash(bin2hex(random_bytes(16)),PASSWORD_DEFAULT); // je crée un nouveau mot de passe
            $user->setPassword($newPassword);
            $user->setRoles(["ROLE_DELETED"]); // je remplace le rôle initial par "rôle supprimé"
            $entityManager->persist($user); // je persiste l'utilisateur 
            $entityManager->flush(); // j'enregistre le changement en base de données 
            $session->invalidate(); // j'invalide la session 
            $this->addFlash('success', 'Votre compte a été supprimé avec succès'); // message de succès
            return $this->redirectToRoute('app_home'); // redirection vers page d'accueil

        }catch (\Exception $e){
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de votre compte.'); // message d'erreur
            return $this->redirectToRoute('app_home'); // redirection vers page d'accueil
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
