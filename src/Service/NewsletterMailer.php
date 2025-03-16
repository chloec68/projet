<?php

namespace App\Service; 

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class NewsletterMailer
{
    private MailerInterface $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendWelcomeEmail($recipientEmail)
    {
        $welcomeEmail = (new TemplatedEmail())
        ->from('test@localhost')
        ->to($recipientEmail)
        ->subject('Inscription Newsletter')
        ->locale('fr')
        ->context([
            'recipientEmail' => $recipientEmail
        ])
        ->htmlTemplate('newsletter/welcome-email.html.twig')
        ->textTemplate('newsletter/welcome-email.txt.twig');
        
        try{
            $this->mailerInterface->send($welcomeEmail);
        }catch(TransportExceptionInterface $e){
            $this->logger->error("Échec de l'envoi de l'email de bienvenue à " . $recipientEmail . ": " . $e->getMessage());
        }
    }
}



