<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    /**
     * @Route("/email")
     */
    public function sendMail(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('h91fyx9ytnuoj5c9mru1@joachimiak-adrian.pl')
            ->to(new Address('oj5c9mru1@joachimiak-adrian.pl', 'Support'))
            ->subject('Notification')
            ->textTemplate('email/notification.txt.twig')
            ->htmlTemplate('email/notification.html.twig');

        $mailer->send($email);

        return new Response('Email sent');
    }
}