<?php

namespace App\Service; 

use App\Repository\NewsletterRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Mailer
{
    private MailerInterface $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendNewsletter($recipientEmail, $newsletterContent)
    {   
        $newsletter = (new TemplatedEmail())
        ->from('test@localhost')
        ->to($recipientEmail)
        ->subject('Newsletter')
        ->locale('fr')
        ->context([
            'recipientEmail' => $recipientEmail,
            'newsletterContent' => $newsletterContent
        ])
        ->htmlTemplate('newsletter/email-content.html.twig');
  
            $this->mailerInterface->send($newsletter);
    }

    public function sendOrderConfirmation($emailAddress,$pickUpTime,$orderReference,$pickUpPoint)
    {
        $confirmationEmail = (new TemplatedEmail())
        ->from('test@localhost')
        ->to($emailAddress)
        ->subject('Votre commande '. $orderReference . ' est confirmée!')
        ->locale('fr')
        ->html('<p>Votre commmande est confirmée et pourra être retirée à partir du</p>' . $pickUpTime->format('d/m/Y') . '<p> aux horaires d\'ouverture de votre point de retrait :<p>' . $pickUpPoint);

        $this->mailerInterface->send($confirmationEmail);
    }
}



