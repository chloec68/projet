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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    #[Route('/home/newsletterSubscription', name:'newsletter-subscription')]
    public function index(ProductRepository $productRepository,RecipientRepository $recipientRepository,NewsletterRepository $newsletterRepository,EntityManagerInterface $entityManager, Request $request, NewsletterMailer $mailer): Response
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
            'newsletterForm' => $newsletterForm->createView(),
            'ephemeral' => $ephemeral,
            'meta_description' => 'Bienvenue sur le site de la Brasserie locale et artisanale Sainte Cru sur laquelle vous pourrez découvrir nos produits et les acheter directement en ligne'
        ]);
    }

    #[Route('/home/newsletterUnsubscription/{recipientEmail}', name:'newsletter-unsubscribe')]
    public function newsletterUnsubscribed(RecipientRepository $recipientRepository,EntityManagerInterface $entityManager, string $recipientEmail)
    {
        $recipient = $recipientRepository->findByEmail($recipientEmail);
        if($recipient){
            $entityManager->remove($recipient);
            $entityManager->flush();
        }

        return $this->render('newsletter/unsubscribed.html.twig',[
            'controllerName' => 'Controller'
        ]);
    }

    #[Route('/newsletter/send/{idNewsletter}', name:'send-newsletter')]
    public function sendNewsletter(NewsletterMailer $mailer, RecipientRepository $recipientRepository, NewsletterRepository $newsletterRepository, int $idNewsletter )
    {
        $recipients = $recipientRepository->findAll();
        $newsletter = $newsletterRepository->find($idNewsletter);
        $newsletterContent = $newsletter->getNewsletterContent();

        if($recipients){
            foreach($recipients as $recipient){
                $recipientEmail = $recipient->getRecipientEmail();
                $mailer->sendNewsletter($recipientEmail,$newsletterContent);
            }
        }

        return $this->redirectToRoute('admin_newsletter_index');
    }
}
