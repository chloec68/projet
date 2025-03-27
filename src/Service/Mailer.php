<?php
//Définition de l'espace de noms en vue de structurer le code et gérer les conflits de noms
namespace App\Service; 
//Importation de la classe TemplatedEmail de Symfony, utilisée pour créer des emails avec un template Twig
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
//Importation de l'interface MailerInterface qui définit les méthodes nécessaires pour l'envoi d'emails
use Symfony\Component\Mailer\MailerInterface;
//Importation de l'exception qui gère les erreurs liées à l'envoi d'emails
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
//Définition de la classe Mailer
class Mailer
{   
    //Déclaration de la variable privée $mailerInterface, instance de MailerInterface
    private MailerInterface $mailerInterface;
    //Constructeur
    public function __construct(MailerInterface $mailerInterface)
    {   //Stockage de l'interface dans une variable pour pouvoir l'utiliser dans les méthodes
        $this->mailerInterface = $mailerInterface;
    }
    //Méthode permettant d'envoyer les newsletter
    //En paramètres, elle recçoit l'email du destinataire et le contenu de l'email
    public function sendNewsletter($recipientEmail, $newsletterContent)
    {   //Création de l'email
        $newsletter = (new TemplatedEmail())
        ->from('test@localhost') //Email de l'expéditeur 
        ->to($recipientEmail) //Email du destinataire
        ->subject('Newsletter') //Objet de l'email
        ->locale('fr') //Langue de l'email definie sur 'français'
        ->context([ //Le contexte permet d'envoyer des données à la vue (template Twig)
            'recipientEmail' => $recipientEmail,
            'newsletterContent' => $newsletterContent
        ])
        ->htmlTemplate('newsletter/email-content.html.twig');//Spécification du template twig
  
            $this->mailerInterface->send($newsletter);//appel de la méthode send de l'interface pour envoyer l'email
    }

    //Méthode permettant d'envoyer l'email de confirmation de commande
    public function sendOrderConfirmation($emailAddress,$pickUpTime,$orderReference,$pickUpPoint)
    {
        $confirmationEmail = (new TemplatedEmail())
        ->from('test@localhost')
        ->to($emailAddress)
        ->subject('Votre commande '. $orderReference . ' est confirmée!')
        ->locale('fr')
        ->html('<p>Votre commmande est confirmée et pourra être retirée à partir du</p>' . $pickUpTime->format('d/m/Y') .'<p> aux horaires d\'ouverture de votre point de retrait :<p>' . $pickUpPoint);

        $this->mailerInterface->send($confirmationEmail);
    }
}



