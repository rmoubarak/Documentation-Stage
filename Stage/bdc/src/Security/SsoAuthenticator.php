<?php

namespace App\Security;

use App\Entity\Acces;
use App\Entity\Utilisateur;
use App\Services\Agent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class SsoAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private RouterInterface $router,
        private Agent $agent
    )
    {}

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        // if there is already an authenticated user (likely due to the session)
        // then return false and skip authentication: there is no need.
        if ($this->security->getUser()) {
            return false;
        }

        return true;
        //return $request->server->has('HTTP_ADUSER');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->server->get('HTTP_ADUSER');

        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $user = $this->em
            ->getRepository(Utilisateur::class)
            ->findOneBy(['login' => $apiToken, 'actif' => true]);

        // Si l'utilisateur n'existe pas, on le récupère de l'AD ; sinon, on le met à jour
        if (!$user) {
            $user = $this->agent->add($apiToken);
        } else {
            //$user = $this->agent->update($user);
        }

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('User not found');
        }

        // Mise en session de la photo
        //$ldapUser = $this->ldap->findUserByLogin($user->getLogin());

        //if ($this->ldap->checkUser($ldapUser)) {
        //    $request->getSession()->set('profile_picture', $this->ldap->getJpegPhoto($ldapUser));
        //}

        return new SelfValidatingPassport(new UserBadge($apiToken));
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

        $request->getSession()->getFlashBag()->add('success', 'Authentification réussie');

        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        //return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        return new RedirectResponse($this->router->generate('default_failed'));
    }
}