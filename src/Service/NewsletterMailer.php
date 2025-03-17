<?php

namespace App\Service; 

use App\Repository\NewsletterRepository;
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
}



