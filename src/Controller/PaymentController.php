<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Entity\Establishment;
use Doctrine\ORM\EntityManager;
use App\Form\CollectionFormType;
use App\Form\IdentificationFormType;
use App\Repository\EstablishmentRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PaymentController extends AbstractController
{

      #[Route('/payment/identification', name:'app_payment-identification')]
    public function identification(Request $request, SessionInterface $session): Response
    {   
        // création du formulaire 
        $identificationForm = $this->createForm(IdentificationFormType::class);
        // traitement de la requête
        $identificationForm->handleRequest($request); 
        // si formulaire soumis et valide
        if ($identificationForm->isSubmitted() && $identificationForm->isValid()) {
            //récupération des données du formulaire
            $data = $identificationForm->getData();
            //enregistrement en session
            $session->set('identificationData',$data);
            // redirection vers la page choisir point de retrait
            dd($session);
            return $this->redirectToRoute('app_payment-collection');
        }

        return $this->render('/payment/identification.html.twig',[
            'identificationForm' => $identificationForm,
        ]);
    }

    #[Route('/payment/collection', name:'app_payment-collection')]
    public function collection(EstablishmentRepository $establishmentRepository, Request $request, SessionInterface $session): Response
    {   
        // récupérer les établissement en bdd pour les envoyer à la vue 
        $establishments = $establishmentRepository->findAll([],['establishmentName'=>'ASC']);
        // créer le formulaire permettant de connaître le choix de l'utilisateur
        $collectionForm = $this->createForm(CollectionFormType::class);
        //traitement de la requête envoyée par le client via le formulaire
        $collectionForm->handleRequest($request);
        //si le formulaire est soumis et valide
        if ($collectionForm->isSubmitted() && $collectionForm->isValid()){
            // j'extraie les data du formulaire (le formulaire contient un array avec "establishmentName" => "2") et les stocke dans une variable
            // en récupérant seulement l'id (la valeur associée à la clé), getData() récupère l'objet lié ; 
            // sinon on enregistre un tableau avec la clé establishmentName avec l'objet pour valeur
            $establishment = $collectionForm->get('establishmentName')->getData();
            // j'enregistre l'objet contenu dans $establishment en session 
            $session->set('establishment',$establishment);
            // redirection vers la page paiement de Stripe 
            return $this->redirectToRoute('app_payment-checkout');
        }

        return $this->render('/payment/collection.html.twig', [
            'establishments' => $establishments,
            'collectionForm' => $collectionForm
        ]);
    }

    #[Route('/payment/recap', name:'app_payment-recap')]
    public function recap():Response
    {

    }

    
    #[Route('/payment/checkout', name:'app_payment-checkout')]
    public function checkout(SessionInterface $session)
    {   
        $priceTotal = $session->get('priceTotal');
        $identificationData = $session->get('identificationData');
        $userEmail = $identificationData['orderEmail'];
        
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Commande',  // Nom générique ou autre
                        ],
                        'unit_amount' => $priceTotal * 100,  // Montant en cents (exemple pour 5.00 USD => 500)
                    ],
                    'quantity' => 1,  // Tu peux juste mettre une quantité de 1 si tu passes un seul montant
                ],
            ],
            'customer_email'=> $userEmail,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html.twig',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html.twig',
            ]);
            return new RedirectResponse($checkout_session->url);
        }

        #[Route('/payment/checkout/success', name:'app_payment-checkout_success')]
        public function success(SessionInterface $session, Security $security, EntityManager $entityManager):Response
        {
            $order = new Order;
            $order->setOrderDateOfPlacement(now());
            $order->setOrderReference(uniqid('stecru-e'));

            $userFirstName = $session->get('orderUserFirstName');
            $order->setOrderUserFirstName($userFirstName);

            $userLastName = $session->get('orderUserLastName');
            $order->setOrderUserLastName($userLastName);

            $email = $session->get('orderEmail');
            $order->setOrderEmail($email);

            $cart = $session->get('cart');
            $priceTotal= $cart['priceTotal'];
            $order->setOrderTotal($priceTotal);

            foreach ($cart as $product => $quantity) {
                $order->add($product);
            }
            $establishment = $session->get('establishment');
            $order->setEstablishment($establishment);

            $appUser = $security->getUser();

            if(isset($user)){
                $order->setAppUser($appUser);
            }

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->render('/payment/success.html.twig', [
                'controller_name' => 'Paymentcontroller',
            ]);
         }

        #[Route('/payment/checkout/success', name:'app_payment-checkout_error')]
        public function error(SessionInterface $session):Response
        { 
            return $this->render('/payment/error.html.twig', [
                'controller_name' => 'Paymentcontroller',
            ]);
        }
    }
        
   
