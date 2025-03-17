<?php

namespace App\Controller;


use App\Entity\Recipient;
use App\Form\RecipientFormType;
use App\Service\NewsletterMailer;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Service\VATpriceCalculator;
use App\Repository\ProductRepository;
use App\Repository\RecipientRepository;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    #[Route('/home/newsletterSubscription', name:'newsletter-subscription')]
    public function index(RecipientRepository $recipientRepository,ProductRepository $productRepository,NewsletterRepository $newsletterRepository, SessionInterface $session,EntityManagerInterface $entityManager, Request $request, NewsletterMailer $mailer): Response
    {   
        $recipient = new Recipient();
        $newsletterForm = $this->createForm(RecipientFormType::class,$recipient);
        $newsletterForm->handleRequest($request);

        if($newsletterForm->isSubmitted() && $newsletterForm->isValid()){
            try{
                $entityManager->persist($recipient);
                $entityManager->flush();
                } catch (UniqueConstraintViolationException $e){
                    $this->addFlash('error','Vous êtes déjà inscrit à la newsletter');
                    return $this->redirectToRoute('app_home');
                }
                
            $recipients = $recipientRepository->findAll(); 
            foreach($recipients as $recipient){
                $mailer->sendNewsletter($recipient->getRecipientEmail());
            }

            $this->addFlash('success','Vous êtes inscrit à la newsletter');
        }
       
        $permanent = $productRepository->findByPermanency(true,1);

        $ephemeral = $productRepository->findByPermanency(false,1);

        return $this->render('home/index.html.twig', [
            'permanent' => $permanent,
            'ephemeral' => $ephemeral,
            'newsletterForm' => $newsletterForm->createView(),
            'meta_description' => 'Bienvenue sur le site de la Brasserie locale et artisanale Sainte Cru sur laquelle vous pourrez découvrir nos produits et les acheter directement en ligne'
        ]);
    }

    // USER PROFILE
    #[Route('/user', name: 'app_user')]
    public function userProfile(Security $security):Response
    {   
        $user = $security->getUser();
  
        return $this->render('home/profile.html.twig', [
            'user' => $user,
            'meta_description' => ''
        ]);
    }

    // USER PROFILE > ORDER 
    #[Route('/user/order/{id}', name:'app_order')]
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
    #[Route('/user/favorites', name:'app_favorites')]
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
}
