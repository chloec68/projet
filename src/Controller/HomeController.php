<?php

namespace App\Controller;

use App\Service\Mailer;
use App\Entity\Recipient;
use App\Form\RecipientFormType;
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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{   
    #[Route('/',name: 'root')]
    public function redirectToHome(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'app_home')]
    #[Route('/home/newsletterSubscription', name:'newsletter-subscription')]
    public function index(ProductRepository $productRepository,RecipientRepository $recipientRepository,NewsletterRepository $newsletterRepository,EntityManagerInterface $entityManager, Request $request): Response
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

            $this->addFlash('success','Vous êtes inscrit à la newsletter');
        }

        $permanent = $productRepository->findByPermanency(true,1);

        $ephemeral = $productRepository->findByPermanency(false,1);

        return $this->render('home/index.html.twig', [
            'permanent' => $permanent,
            'newsletterForm' => $newsletterForm->createView(),
            'ephemeral' => $ephemeral,
            'meta_description' => 'Bienvenue sur le site de la Brasserie locale et artisanale Sainte Cru sur laquelle vous pourrez découvrir nos produits et les acheter directement en ligne'
        ]);
    }

    #[Route('/home/newsletter-unsubscribe/{recipientEmail}', name:'newsletter-unsubscribe')]
    public function newsletterUnsubscribed(RecipientRepository $recipientRepository,EntityManagerInterface $entityManager, string $recipientEmail)
    {   
        // je récupère l'adresse email à partir de l'objet destinataire passé en argument 
        // en appelant la méthode findByEmail du RecipientRepository
        $recipient = $recipientRepository->findByEmail($recipientEmail);
        // si la variable $recipient n'est pas falsy
        if(!empty($recipient)){
            // je supprime l'objet destinataire de la table en base de données 
            $entityManager->remove($recipient);
            $entityManager->flush();
        }else{
            // si la variable recipient est falsy, j'ajoute un message flash en session 
            $this->addFlash('error','Utilisateur non trouvé - la désinscription a échoué');
            // et redirige vers la page d'accueil (où j'affiche les messages)
            return $this->redirectToRoute('app_home');
        }
        // la méthode rend la vue unsubscribed.html.twig 
        return $this->render('/newsletter/unsubscribed.html.twig',[
            'meta_description' => "Désinscription réussie de notre newsletter. Vous ne recevrez plus d'emails."
        ]);
    }

    #[Route('/newsletter/send/{idNewsletter}', name:'send-newsletter')]
    public function sendNewsletter(Mailer $mailer, RecipientRepository $recipientRepository,
    NewsletterRepository $newsletterRepository, int $idNewsletter )
    {   
        // récupère tous les destinataires
        $recipients = $recipientRepository->findAll();
        // récupère l'objet newsletter à partir de l'id passé en param
        $newsletter = $newsletterRepository->find($idNewsletter);
        // récupère le contenu de la newsletter
        $newsletterContent = $newsletter->getNewsletterContent();
        // si de destinataires sont trouvés
        if(!empty($recipients)){
            // pour chaque destinataire
            foreach($recipients as $recipient){
                // récupère l'email 
                $recipientEmail = $recipient->getRecipientEmail();
                // et envoie la newsletter
                $mailer->sendNewsletter($recipientEmail,$newsletterContent);
            }
        }
        // redirection sur page principale du panneau administrateur
        return $this->redirectToRoute('admin_newsletter_index');
    }

    
}
