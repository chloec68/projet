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

    public function sendNewsletter($recipient)
    {
        $newsletter = (new TemplatedEmail())
        ->from('test@localhost')
        ->to($recipient)
        ->subject('Newsletter')
        ->locale('fr')
        ->context([
            'recipients' => $recipient
        ])
        ->htmlTemplate('newsletter/email-content.html.twig');
  
            $this->mailerInterface->send($newsletter);
    }
}



