<?php

namespace App\Security;

use App\Entity\Acces;
use App\Services\Agent;
use App\Services\Captcha;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class FormLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'security_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private Captcha $captcha,
        private EntityManagerInterface $em,
        private Agent $agent)
    {}

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        // Vérif du captcha
        $captcha_challenge_id = $request->request->get('captcha_challenge_id');
        $captcha_answer = $request->request->get('captcha_answer');

        if (!$this->captcha->check($captcha_challenge_id, $captcha_answer)) {
            throw new CustomUserMessageAuthenticationException('Captcha non valide');
        }

        $passport = new Passport(
            new UserBadge($credentials['username']),
            new PasswordCredentials($credentials['password']),
            [new RememberMeBadge()]
        );

        $passport->addBadge(new CsrfTokenBadge('authenticate', $credentials['csrf_token']));
        $passport->addBadge(new PasswordUpgradeBadge($credentials['password']));

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Log de la connexion
        $acces = new Acces();
        $acces->setUtilisateur($token->getUser());
        $acces->setDate(new \DateTime());
        $acces->setIp($request->getClientIp());
        $this->em->persist($acces);

        $this->em->flush();

        // MAJ du user
        $this->agent->update($token->getUser());

        return null;
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
            'captcha' => $request->request->get('captcha-response'),
        ];
        $request->getSession()->set('_security.last_username', $credentials['username']);

        return $credentials;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
