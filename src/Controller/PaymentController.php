<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Stripe\Stripe;
use Dompdf\Options;
use App\Entity\Bill;
use App\Entity\Order;
use App\Service\Mailer;
use App\Entity\OrderProduct;
use Stripe\Checkout\Session;
use App\Entity\Establishment;
use App\Form\PickUpPointFormType;
use App\Repository\OrderRepository;
use App\Service\VATpriceCalculator;
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
            return $this->redirectToRoute('app_payment-recap');
        }

        return $this->render('/payment/pickUpPoint.html.twig', [
            'establishments' => $establishments,
            'pickUpPointForm' => $pickUpPointForm,
            'meta_description' => "Vous êtes à l'étape 2/3 du paiement. Choisissez le point de retrait où vous souhaitez retirer votre commande"
        ]);
    }

    #[Route('/payment/recap', name:'app_payment-recap')]
    public function recap(SessionInterface $session, ProductRepository $productRepository, VATpriceCalculator $priceCalculator):Response
    {  
        $cart = $session->get('cart');
        $products = [];
        $nbItems = 0;
        $total = 0;
        $subTotals=[];
        $quantities = [];
        
        //pour chaque élément du tableau associatif $cart 
        foreach ($cart as $id=> $quantity) {
            // je récupère le prodiut à partir de l'id 
            $product = $productRepository->find($id);
            // calcul du sous-total
            $subTotal = number_format($priceCalculator->vatPriceSubTotal($product,$quantity),2,'.',' ');
            // j'ajoute le sous-total à un tableau associatif $id => $subTotal 
            $subTotals[$product->getId()] = $subTotal;
            // j'additionne les quantités pour obtenir le nombre total de produits 
            $nbItems += $quantity;
            // j'additionne les sous-totaux pour obtenir le total
            $total+=$subTotal;
            $totalformatted = number_format($total,2,'.',' ');
            // j'ajoute le total en session 
            $session->set('total',$totalformatted);
            // j'ajoute le produit à un tableau associatif indexé pour pouvoir les encoyer à la vue et les y afficher à l'aide d'une boucle
            $products[]=$product;
            // j'ajoute la quantité associée à chaque produit à un tableau associatif avec l'id produit comme index pour pouvoir récupérer la quantité associée au produit dans la vue
            $quantities[$product->getId()] = $quantity;
        }

        return $this->render('/payment/recap.html.twig', [
            'products' => $products,
            'total' => $totalformatted,
            'nbItems' => $nbItems,
            'subTotals' => $subTotals,
            'quantities' => $quantities,
            'meta_description' => "Récapitulatif de votre commande"
        ]);
    }

    
    #[Route('/payment/checkout', name:'app_payment-checkout')]
    public function checkout(SessionInterface $session, ProductRepository $productRepository, VATpriceCalculator $priceCalculator)
    {   
        $cart = $session->get('cart');
        $total = $session->get('total');

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
                        'unit_amount' => $total * 100,  // Montant en cents (exemple pour 5.00 EUR => 500)
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
            $total= $session->get('total');
            $order->setOrderTotal($total);
            
            // récupération du tableau panier enregistré en session et itération dans le tableau associatif
            $cart = $session->get('cart');
            foreach ($cart as $id => $quantity) {
                // récupération des objets produit en fonction de l'id contenu dans le tableau associatif panier 
                $product = $productRepository->find($id);

                // mise à jour du stock 
                $stock = $product->getProductStock();
                $updatedStock = $stock - $quantity;
                $product->setProductStock($updatedStock);
                $entityManager->persist($product);
                $entityManager->flush();

                // création d'un nouvel objet OrderProduct
                $orderProduct = new OrderProduct();
                // ajout du produit à OrderProduct
                $orderProduct->setAppProduct($product);
                // ajout de la quantity 
                $orderProduct->setQuantity($quantity);
                //ajout du prix au jour de la commande
                $price = $product->getProductPrice();      
                $orderProduct->setProductPrice($price);
                // ajout de chaque produit et de la quantité (valeur) associée à la commande
                $order->addOrderProduct($orderProduct);
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
            $bill->setBillTotalVat($total);  
            // ajout de la date à la facture
            $bill->setBillDate(new \DateTime()); 

            //récupération de la collection d'objets Produit de la commande
            $orderproducts = $order->getOrderProducts();
    
            //initialisation du prix total en dehors de la boucle pour qu'il ne soit pas reset à 0 à chaque tour de boucle
            $noVatPriceTotal = 0;
            //pour chaque produit
            foreach ($orderproducts as $orderproduct) {
                // récupérer le prix HT de chaque produit
                $priceNoVat = $orderproduct->getProductPrice();
                // récupérer la quantité associée à chaque produit 
                $quantity = $orderProduct->getQuantity();
                // calculer sous-total  
                $subTotal = $quantity * $priceNoVat;
                // additionner chaque prix HT et les ajouter au total
                $noVatPriceTotal += $subTotal;
            }
            $noVatPriceTotalformatted = number_format($noVatPriceTotal,2,'.',' ');
            $bill->setBillTotalBeforeVat($noVatPriceTotalformatted);

            $bill->setAppOrder($order);

            $entityManager->persist($bill);
            $entityManager->flush();

            // REINITILISATION DU PANIER 

            if($session->has('cart')){
                $session->remove('cart');
            }
           
            return $this->render('/payment/success.html.twig', [
                'order' => $order,
                'meta_description' => "Le paiement a réussi. Nous vous remercions pour votre commande."
            ]);
         }

        #[Route('/payment/checkout/bill', name:'app_payment-checkout-bill')]
        public function billGenerator(SessionInterface $session, OrderRepository $orderRepository, EstablishmentRepository $establishmentRepository, VATpriceCalculator $priceCalculator, Mailer $mailer):Response
        {   
            //récupération de l'id de la commmande
            $orderId = $session->get('orderId'); 
            //récupération de la commande à partir de l'id
            $order = $orderRepository->find($orderId);
            // je récupère l'établissement id=1 pour afficher le nom de la brasserie (le vendeur) en haut de page 
            $id=1;
            $seller = $establishmentRepository->find($id);
            // récupération des produits liés à la commande 
            $orderproducts = $order->getOrderProducts();
            //initialisation du total HT
            $totalNoVat = 0;
            //initialisation d'un tableau pour stocker les sous-totaux
            $subTotals = [];
            //initialisation du nombre total d'articles
            $nbItems = 0;
            foreach($orderproducts as $orderProduct){
                $quantity = $orderProduct->getQuantity();
                $product = $orderProduct->getAppProduct();
                //récupération sous-totaux
                $subTotal = $priceCalculator->vatPriceSubTotal($product,$quantity);
                $subTotals[$product->getId()]=$subTotal;
                //addition des quantités pour obtenir nombre total d'articles
                $nbItems += $quantity;
            }
            //calcul de la TVA : 
            $bill = $order->getBill();
            $totalNoVat = $bill->getBillTotalBeforeVat();
            $total = $order->getOrderTotal();
            $vat = $total - $totalNoVat;

            //date de retrait (J+1)
            $pickUpTime = new \DateTime();
            $pickUpTime->modify('+2 days');
            $pickUpTime->format('dd.mm.Y');

            $options = new Options();
            $options->set('defaultFont', 'helvetica');
            //création d'une instance de Dompf()
            $dompdf = new Dompdf($options);
  
            //envoie du contenu HTML à la vue 
            $html = $this->renderView('/payment/bill.html.twig',[
                'order' => $order,
                'seller' => $seller,
                'vat' => $vat,
                'pickUpTime'=>$pickUpTime,
                'subTotals'=>$subTotals,
                'totalNoVat' => $totalNoVat,
                'nbItems' => $nbItems
            ]);

            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            //charge HTML dans Dompf
            $dompdf->loadHtml($html);
            //configuration format et orientation page
            $dompdf->setPaper('A4', 'portrait');
            //rend PDF
            $dompdf->render();
            //envoie PDF au navigateur en générant une réponse manuellement 

            //envoie email confirmation 
            $orderReference = $order->getOrderReference();
            $emailAddress = $order->getOrderEmail();
            $pickUpPoint = $order->getEstablishment()->getEstablishmentName();
            $mailer->sendOrderConfirmation($emailAddress,$pickUpTime,$orderReference,$pickUpPoint);

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
        
   
