<?php

namespace App\Services;

use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class TwoFactorAuthCodeMailer implements AuthCodeMailerInterface
{
    public function __construct(private MailerInterface $mailer, private Environment $environment)
    {}

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $message = (new TemplatedEmail())
            ->subject('[Fonds de tarte] Votre code de sécurité')
            ->from('nepasrepondre@hautsdefrance.fr')
            ->to($user->getEmailAuthRecipient())
            ->htmlTemplate('email/2fa_send_auth_code.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {

        }
    }
}