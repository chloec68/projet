<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Entity\Establishment;
use App\Form\CollectionFormType;
use App\Form\IdentificationFormType;
use App\Repository\EstablishmentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;






class PaymentController extends AbstractController
{

      #[Route('/payment/identification', name:'app_payment-identification')]
    public function identification(Request $request, SessionInterface $session): Response
    {   

        $identificationForm = $this->createForm(IdentificationFormType::class);

        $identificationForm->handleRequest($request); 

        if ($identificationForm->isSubmitted() && $identificationForm->isValid()) {

            $data = $identificationForm->getData();

            $session->set('identificationData',$data);

            // dd($session);
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
        // création d'un nouvel objet établissement qui pourra ultérieurement être passé à la commande
        $establishment = new Establishment();
        // créer le formulaire permettant de connaître le choix de l'utilisateur
        $collectionForm = $this->createForm(CollectionFormType::class,$establishment);
        //traitement de la requête envoyée par le client via le formulaire
        $collectionForm->handleRequest($request);
        // dd($request);
        //si le formulaire est soumis et valide
        if ($collectionForm->isSubmitted() && $collectionForm->isValid()){
            // j'extraie les data du formulaire et les stocke dans une variable
            $data = $collectionForm->getData();
            $establishmentName = $data->getEstablishmentName();
            // dd($establishmentName);
            $session->set('establishment',$establishment);
            // dd($session);
        }

        return $this->render('/payment/collection.html.twig', [
            'establishments' => $establishments,
            'collectionForm' => $collectionForm
        ]);
    }

    // #[Route('/payment/identification', name:'app_payment-identification')]
    // public function identification(Request $request, SessionInterface $session, Security $security, ProductRepository $productRepository): Response
    // {   
    //     // je crée un nouvel objet commande 
    //     $order = new Order();
    //     // je crée le formulaire associé à la classe commande delaquelle j'ai instancié l'objet
    //     $identificationForm = $this->createForm(IdentificationFormType::class, $order);
    //     // je traite la requête http envoyée par le client après soumission du formulaire
    //     $identificationForm->handleRequest($request); 
    //     // si le formulaire a été soumis et que les champs sont valides 
    //     if ($identificationForm->isSubmitted() && $identificationForm->isValid()) {
    //         // je récupère les data du formulaire 
    //         $data = $identificationForm->getData();
    //         //j'ajoute la date et l'heure à la commande
    //         $now = now();
    //         $order->setDateOfPlacement($now);
    //         //je génère un numéro de commande 
    //         $orderReference = uniqid('stecru-e');
    //         $order->setorderReference($orderReference);
    //         //j'ajoute le prénom à la commande
    //         $userFirstName = $data->getOrderUserFirstName();
    //         $order->setOrderUserFirstName($userFirstName);
    //         //j'ajoute le nom à la commande 
    //         $userLastName = $data->getOrderUserLastName();
    //         $order->setOrderUserLastName($userLastName);
    //         //j'ajoute le total à la commande, lequel est enregistré en session
    //         $orderTotal = $session->get('priceTotal');
    //         $order->setOrderTotal($orderTotal);
    //         //j'ajoute l'email à la commande
    //         $userEmail = $data->getOrderEmail();
    //         $order->setOrderEmail($userEmail);
    //         //j'ajoute le panier à la commande 
    //         $cart = $session->get('cart');
    //         foreach ($cart as $productId => $quantity) {
    //             $product = $productRepository->find($productId);
    //             if($product){
    //                 $order->addProduct($product,$quantity);
    //             }
    //         }
    //         // s'il existe en session, j'ajoute l'objet user
    //         $user = $security->getUser();
    //         if($user){
    //             $order->setAppUser($user);
    //         }
    //         $session->set('order',$order);

    //         return $this->redirectToRoute('app_payment-collection');
    //     }

    //     return $this->render('/payment/identification.html.twig',[
    //         'identificationForm' => $identificationForm,
    //     ]);
    // }
   
    // #[Route('/payment/collection', name:'app_payment-collection')]
    // public function collection(Request $request, SessionInterface $session, EstablishmentRepository $establishmentRepository): Response
    // {   
    //     //vérifier si le formulaire a été soumis pour ne pas directement "entrer dans le controller" dès la redirection
    //     if(isset($_POST['submit'])){
    //         //récupérer la commande en session
    //         $order = $session->get('order');
    //         //récupérer le résultat de la requête 
    //         $selectedOption = $request->request->all(); 
    //         //si $chosenOption est définie
    //             if(isset($selectedOption)){
    //                 //créer un objet établissement 
    //                 $establishment = new Establishment ; 
    //                 //si la variable $selectedOption est définie/ !== NULL
    //                 if($selectedOption['selected_option'] == 'option2'){
    //                     $name = 'Brasserie';
    //                 }else{
    //                     $name = 'Entrepot';   
    //                 }
    //                 $establishment = $establishmentRepository->findByName($name);
    //                 // ajouter l'établissement 
    //                 $order->setEstablishment($establishment);

    //                 $session->set('order',$order);
    //             }

    //             return $this->redirectToRoute('app_payment-charge');
    //     }
    //     return $this->render('/payment/collection.html.twig', [
    //         'controller_name' => 'PaymentController',
    //     ]);
    // }

    
    #[Route('/charge/order', name:'app_payment-charge')]
    public function charge(Request $request, ProductRepository $productRepository,): Response{

        $stripeSecretKey = 'sk_test_51QuA8b2M19Cf3LVfWJz7Rfz26oC27Xt5ZkaYQW1sYqSf0QKgVl5sdPkOLUZUpYtVWLs4GtX6BE0QkSNEtxJo7bFJ00jaZoqcwT';
        
        Stripe::setApiKey($stripeSecretKey);
        Stripe::setApiVersion('2025-01-27.acacia');

        $order = $session->get('order');

        // header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $checkout_session = Session::create([
            'line_items' => [
               
            ],
            'customer_email'=> $order,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html.twig',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html.twig',
            ]);
    
        }

    }
        
   
