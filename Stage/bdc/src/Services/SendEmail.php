<?php

namespace App\Services;

use App\Entity\Utilisateur;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendEmail
{
    /**
     * SendEmail constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(private MailerInterface $mailer)
    {}

    /**
     * @param Utilisateur $utilisateur
     * @return bool
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function utilisateurNew(Utilisateur $utilisateur): bool
    {
        $message = (new TemplatedEmail())
            ->subject('[Fond de tarte] Création de votre compte')
            ->from('nepasrepondre@hautsdefrance.fr')
            ->to($utilisateur->getEmail())
            ->htmlTemplate('email/utilisateur_creation.html.twig')
            ->context([
                'utilisateur' => $utilisateur,
            ])
        ;

        try {
            $this->mailer->send($message);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function passwordForget(Utilisateur $utilisateur)
    {
        $message = (new TemplatedEmail())
            ->subject('[Fond de tarte] Modification de votre mot de passe')
            ->from('nepasrepondre@hautsdefrance.fr')
            ->to($utilisateur->getEmail())
            ->htmlTemplate('email/utilisateur_password_forget.html.twig')
            ->context([
                'utilisateur' => $utilisateur,
            ])
        ;

        try {
            $this->mailer->send($message);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}
