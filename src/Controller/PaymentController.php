<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use Stripe\Checkout\Session;
use App\Entity\Establishment;
use App\Form\CollectionFormType;
use App\Form\IdentificationFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            'meta_description' => "Vous êtes à l'étape 1/3 du paiement. Dites-nous qui vous êtes pour que nous puissions préparer votre commande"
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
            'collectionForm' => $collectionForm,
            'meta_description' => "Vous êtes à l'étape 2/3 du paiement. Choisissez le point de retrait où vous souhaitez retirer votre commande"
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
                            'name' => 'Commande',  // Nom générique
                        ],
                        'unit_amount' => $priceTotal * 100,  // Montant en cents (exemple pour 5.00 EUR => 500)
                    ],
                    'quantity' => 1,  // quantité de 1 car un seul montant (total)
                ],
            ],
            'customer_email'=> $userEmail,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/payment/checkout/success',
            'cancel_url' => $YOUR_DOMAIN . '/payment/checkout/cancel',
            ]);
            return new RedirectResponse($checkout_session->url);
        }

        #[Route('/payment/checkout/success', name:'app_payment-checkout-success')]
        public function success(SessionInterface $session, Security $security, EntityManagerInterface $entityManager, ProductRepository $productRepository):Response
        {   
            //COMMANDE
            //création d'un nouvel objet commande 
            $order = new Order;
            //ajout d'un objet DateTime - à vérifier 
            $order->setDateOfPlacement(new \DateTime());
            //ajout d'une référence
            $order->setOrderReference(uniqid('stecru-e'));
            //ajout des données d'identification de l'utilisateur préalablement enregistrées en session si not NULL (existe + déclarée)
            if(isset($identificationData)){
                // récupération du tableau de données d'identification en session
                $identificationData = $session->get('identificationData');
                // récupération de la clé prénom utilisateur et de sa valeur
                $orderFirstName = $identificationData['orderUserFirstName'];
                // ajout prénom utilisateur (clé + valeur)
                $order->setOrderUserFirstName($orderFirstName);
                // idem avec nom de famille et email
                $orderLastName = $identificationData['orderUserLastName'];
                $order->setOrderUserLastName($orderLastName);
                $orderEmail = $identificationData['orderEmail'];
                $order->setOrderEmail($orderEmail);  
            }
            //récupération du total du panier enregistré en session + ajout à la commande
            $priceTotal= $session->get('priceTotal');
            $order->setOrderTotal($priceTotal);
            
            // récupération du tableau panier enregistré en session et itération dans le tableau associatif
            $cart = $session->get('cart');
            foreach ($cart as $id => $quantity) {
                // récupération des objets produit en fonction de l'id contenu dans le tableau associatif panier 
                $product = $productRepository->find($id);
                // ajout de chaque produit et de la quantité (valeur) associée à la commande
                $order->addProduct($product,$quantity);
            }

            // récupération clé en session et si la variable existe et est définie, récupération de la valeur (un objet établissement) et ajout à la commande
            $establishment = $session->get('establishment');
            if(isset($establishment)){
                $order->setEstablishment($establishment);
            }

            // idem 
            $appUser = $security->getUser();
            if(isset($appUser)){
                $order->setAppUser($appUser);
            }

            //FACTURE 
            

            dd($order);

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->render('/payment/success.html.twig', [
                'order' => $order,
                'meta_description' => "Le paiement a réussi. Nous vous remercions pour votre commande."
            ]);
         }

        #[Route('/payment/checkout/cancel', name:'app_payment-checkout-cancel')]
        public function error(SessionInterface $session):Response
        { 
            return $this->render('/payment/cancel.html.twig', [
                'controller_name' => 'Paymentcontroller',
                'meta_description' => "Le paiement a échoué, votre commande n'a pas été enregistrée."
            ]);
        }
    }
        
   
