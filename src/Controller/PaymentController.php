<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Stripe\Stripe;
use Dompdf\Options;
use App\Entity\Bill;
use App\Entity\Order;
use Stripe\Checkout\Session;
use App\Entity\Establishment;
use App\Form\PickUpPointFormType;
use App\Repository\OrderRepository;
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
            // dd($session);
            return $this->redirectToRoute('app_payment-pickUpPoint');
        }

        return $this->render('/payment/identification.html.twig',[
            'identificationForm' => $identificationForm,
            'meta_description' => "Vous êtes à l'étape 1/3 du paiement. Dites-nous qui vous êtes pour que nous puissions préparer votre commande"
        ]);
    }

    #[Route('/payment/pickUpPoint', name:'app_payment-pickUpPoint')]
    public function pickUpPoint(EstablishmentRepository $establishmentRepository, Request $request, SessionInterface $session): Response
    {   
        // récupérer les établissement en bdd pour les envoyer à la vue 
        $establishments = $establishmentRepository->findAll([],['establishmentName'=>'ASC']);
        // créer le formulaire permettant de connaître le choix de l'utilisateur
        $pickUpPointForm = $this->createForm(PickUpPointFormType::class);
        //traitement de la requête envoyée par le client via le formulaire
        $pickUpPointForm->handleRequest($request);
        //si le formulaire est soumis et valide
        if ($pickUpPointForm->isSubmitted() && $pickUpPointForm->isValid()){
            // j'extraie les data du formulaire (le formulaire contient un array avec "establishmentName" => "2") et les stocke dans une variable
            // en récupérant seulement l'id (la valeur associée à la clé), getData() récupère l'objet lié ; 
            // sinon on enregistre un tableau avec la clé establishmentName avec l'objet pour valeur
            $establishment = $pickUpPointForm->get('establishmentName')->getData();
            // j'enregistre l'objet contenu dans $establishment en session 
            $session->set('establishment',$establishment);


            // redirection vers la page paiement de Stripe 
            // dd($session);
            return $this->redirectToRoute('app_payment-checkout');
        }

        return $this->render('/payment/pickUpPoint.html.twig', [
            'establishments' => $establishments,
            'pickUpPointForm' => $pickUpPointForm,
            'meta_description' => "Vous êtes à l'étape 2/3 du paiement. Choisissez le point de retrait où vous souhaitez retirer votre commande"
        ]);
    }

    #[Route('/payment/recap', name:'app_payment-recap')]
    public function recap(SessionInterface $session):Response
    {   

        $cartData = $session->get('cartData');
        // dd($cartData);
        return $this->render('/payment/recap.html.twig', [
            'cartData' => $cartData,
            'meta_description' => "Récapitulatif de votre commande"
        ]);

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
        public function success(EstablishmentRepository $establishmentRepository, SessionInterface $session, Security $security, EntityManagerInterface $entityManager, ProductRepository $productRepository):Response
        {   
            //COMMANDE
            //création d'un nouvel objet commande 
            $order = new Order;
            //ajout d'un objet DateTime - à vérifier 
            $order->setDateOfPlacement(new \DateTime());
            //ajout d'une référence
            $order->setOrderReference(uniqid('stecru-e'));
         
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
            // idem 
            $appUser = $security->getUser();
            if(isset($appUser)){
                $order->setAppUser($appUser);
            }

            // récupération de l'établissement choisi en session
            $establishment = $session->get('establishment');
            // récupération de l'id de l'établissement
            $establishmentId = $establishment->getId();
            // récupération de l'objet via le manager pour que doctrine fasse le lien et ne crée pas de nouvelle entité + réassigne à la variable
            $establishment = $establishmentRepository->find($establishmentId);
            // ajout de l'établissement à la commande 
            $order->setEstablishment($establishment);

            //ajout de la commande en BDD
            $entityManager->persist($order);
            $entityManager->flush();

            $orderId = $order->getId(); 
            $session->set('orderId',$orderId);

            //FACTURE 
            $bill = new Bill();
            // création de la référence facture
            $billReferenceNumber = uniqid('bill');
            //ajout référence à la facture
            $bill -> setBillReferenceNumber($billReferenceNumber);
            // ajout du prix total TTC 
            $bill->setBillTotalVat($priceTotal);  
            // ajout de la date à la facture
            $bill->setBillDate(new \DateTime()); 

            //récupération de la collection d'objets Produit de la commande
            $products = $order->getProducts();
            //initialisation du prix total en dehors de la boucle pour qu'il ne soit pas reset à 0 à chaque tour de boucle
            $noVatPriceTotal = 0;
            //pour chaque produit
            foreach ($products as $product) {
                // récupérer le prix HT de chaque produit
                $priceNoVat = $product->getProductPrice();
                // additionner chaque prix HT et les ajouter au total
                $noVatPriceTotal += $priceNoVat;
            }

            $bill->setBillTotalBeforeVat($noVatPriceTotal);

            $bill->setAppOrder($order);
           
            return $this->render('/payment/success.html.twig', [
                'order' => $order,
                'meta_description' => "Le paiement a réussi. Nous vous remercions pour votre commande."
            ]);
         }

        #[Route('/payment/checkout/bill', name:'app_payment-checkout-bill')]
        public function billGenerator(SessionInterface $session, OrderRepository $orderRepository, EstablishmentRepository $establishmentRepository):Response
        {   
            //récupération des infos nécessaires à la vue
            $orderId = $session->get('orderId'); 
            $order = $orderRepository->find($orderId);
            $cartData = $session->get('cartData');
            $nbItems = $session->get('nbItems');
            $id=1;
            $seller = $establishmentRepository->find($id);
            $products = $order->getProducts();
            $totalNoVat = 0;
            foreach($products as $product){
                $priceNoVat = $product->getProductPrice();
                $totalNoVat += $priceNoVat ; 
            }
            $vat = $order->getOrderTotal() - $totalNoVat ; 
            $pickUpTime = new \DateTime();
            $pickUpTime->modify('+1 day');
            $pickUpTime->format('dd.mm.Y');

            $options = new Options();

            $options->set('defaultFont', 'helvetica');
            //création d'une instance de Dompf()
            $dompdf = new Dompdf($options);

            //récupération du contenu HTML de la vue 
            $html = $this->renderView('/payment/bill.html.twig',[
                'order' => $order,
                'cartData' => $cartData,
                'nbItems' => $nbItems,
                'seller' => $seller,
                'vat' => $vat,
                'pickUpTime'=>$pickUpTime
            ]);
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            //charge HTML dans Dompf
            $dompdf->loadHtml($html);
            //configuration format et orientation page
            $dompdf->setPaper('A4', 'portrait');
            //rend PDF
            $dompdf->render();
            //envoie PDF au navigateur en générant une réponse manuellement 

            return new Response(
                $dompdf->output(), //le contenu PDF généré 
                200, //Code de statut HTTP (OK)
                [   //définition manuelle des entêtes HTTP 
                    'Content-Type' => 'application/pdf', 
                    'Content-Disposition' => 'inline; filename="facture.pdf"'//attachment pour forcer le téléchargement
                ]
            );

            //si j'utilise stream() qui envoie le pdf généré j'ai une erreur de chargement du PDF 
            // $dompdf->stream('facture.pdf', [
            //     'Attachment' => 0 // Affiche le PDF dans la visionneuse sans téléchargement
            // ]);
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
        
   
