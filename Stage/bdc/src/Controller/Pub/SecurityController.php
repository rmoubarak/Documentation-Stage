<?php

namespace App\Controller\Pub;

use App\Entity\Utilisateur;
use App\Form\SecurityPasswordType;
use App\Form\UtilisateurPasswordType;
use App\Services\Captcha;
use App\Services\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/public/security')]
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('public_default_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('public/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout(Request $request): Response
    {
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        $response = new Response();
        $response->headers->clearCookie('PHPSESSID');

        $this->addFlash('success', 'Vous avez été déconnecté.');

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        //return $this->redirectToRoute('security_login');
    }

    #[Route('/failed', name: 'security_failed')]
    public function failed(Request $request): Response
    {
        return $this->render('public/security/failed.html.twig');
    }

    /**
     * Ajout d'un user via le formulaire
     *
     * @param Request $request
     * @param Email $email
     * @return Response
     */
    #[Route('/new', name: 'security_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendEmail $sendEmail,
                        UserPasswordHasherInterface $passwordHasher, Captcha $captcha): Response
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setCreatedAt(new \DateTime());
        $utilisateur->setRole('Public');
        $utilisateur->setActif(1);
        $utilisateur->setLogin('x');

        $form = $this->createForm(UtilisateurPasswordType::class, $utilisateur);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            // Vérif du captcha
            if ($form->isSubmitted() && false == $captcha->check($request->get('captcha_challenge_id'), $request->get('captcha_answer'))) {
                $form->addError(new FormError('Mauvais code captcha'));
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $utilisateur->setLogin($utilisateur->getEmail());

                $password = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
                $utilisateur->setPassword($password);


                $entityManager->persist($utilisateur);
                $entityManager->flush();

                $sendEmail->utilisateurNew($utilisateur);

                $this->addFlash('success', 'Votre compte a été créé. Vous pouvez vous connecter.');

                return $this->redirectToRoute('security_login');
            }
        }

        return $this->render('public/security/new.html.twig', [
            'post' => $utilisateur,
            'form' => $form,
        ]);
    }

    /**
     * Définition d'un mot de passe (Accès par token)
     *
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $passwordHasher
     * @return Response
     */
    #[Route('/motdepasse/{token}', name: 'security_app_password')]
    public function password(Request $request, EntityManagerInterface $entityManager, string $token,
                             UserPasswordHasherInterface $passwordHasher, Captcha $captcha): Response
    {
        $utilisateur = $entityManager
            ->getRepository(Utilisateur::class)
            ->findOneBy(['token' => $token, 'actif' => true]);

        if (!$utilisateur) {
            $this->addFlash('danger', 'Votre compte n\'a pas été identifié.');
        }

        // Vérif de la validité du token
        if ($utilisateur) {
            $date = new \DateTime();
            $date->modify("-24 hours");
            $tokenDate = $date->format('YmdHis');

            if (!$utilisateur->getTokenDate() || $utilisateur->getTokenDate()->format('YmdHis') < $tokenDate) {
                $utilisateur = null;
                $this->addFlash('danger', 'La période de modification de votre mot de passe a expiré.');
            }
        }

        $form = $this->createForm(SecurityPasswordType::class, $utilisateur);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            // Vérif du captcha
            if ($form->isSubmitted() && false == $captcha->check($request->get('captcha_challenge_id'), $request->get('captcha_answer'))) {
                $form->addError(new FormError('Mauvais code captcha'));
            }

            if ($form->isValid()) {
                $password = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
                $utilisateur->setPassword($password);
                $utilisateur->setToken(null);
                $utilisateur->setTokenDate(null);
                $utilisateur->setUpdatedAt(new \DateTime());

                $entityManager->persist($utilisateur);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été modifié avec succès. Vous pouvez vous connecter.');

                return $this->redirectToRoute('security_login');
            }
        }

        return $this->render('public/security/password.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    /**
     * Mot de passe oublié : on renvoie un email
     *
     * @param Request $request
     * @param Email $semail
     * @return Response
     * @throws \Exception
     */
    #[Route('/motdepasseoublie', name: 'security_app_password_forget')]
    public function passwordForget(Request $request, EntityManagerInterface $entityManager, SendEmail $sendEmail, Captcha $captcha): Response
    {
        if ($request->getMethod() == 'POST') {
            $email = $request->get('email');

            $utilisateur = $entityManager
                ->getRepository(Utilisateur::class)
                ->findOneBy(['email' => $email, 'actif' => true]);

            if (!$utilisateur) {
                $this->addFlash('danger', 'Votre compte n\'a pas été identifié.');

                return $this->redirectToRoute('security_app_password_forget');
            }

            // Vérif du captcha
            if (false == $captcha->check($request->get('captcha_challenge_id'), $request->get('captcha_answer'))) {
                $this->addFlash('danger', 'Mauvais code captcha');

                return $this->redirectToRoute('security_app_password_forget');
            }

            // Génération et sauvegarde du token identifiant le compte pour changement de mot de passe
            $token = sha1(random_bytes(10));
            $utilisateur->setToken($token);
            $utilisateur->setTokenDate(new \DateTime());

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            if ($sendEmail->passwordForget($utilisateur)) {
                $this->addFlash('success', 'Un email vous a été envoyé. Veuillez cliquer sur le lien
                qu\'il contient pour définir un nouveau mot de passe.');
            } else {
                $this->addFlash('danger', 'L\'envoi d\'email a échoué.');
            }
        }

        return $this->render('public/security/password_forget.html.twig');
    }

    #[Route('/2faresend', name: 'security_2fa_resend', options: ['expose' => true])]
    #[IsGranted("IS_AUTHENTICATED_2FA_IN_PROGRESS")]
    public function twoFactorResend(CodeGeneratorInterface $codeGenerator): Response
    {
        try {
            $codeGenerator->generateAndSend($this->getUser());

            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('ko');
        }
    }

    #[Route('/captcharefresh', name: 'security_captcha_refresh', options: ['expose' => true])]
    public function captchaRefresh(Captcha $captcha): Response
    {
        $captcha_challenge_id = $captcha->create();
        $captcha_image_url = $captcha_challenge_id ? $captcha->getImageUrl($captcha_challenge_id) : null;

        return $this->render('public/security/_captcha.html.twig', [
            'captcha_challenge_id' => $captcha_challenge_id,
            'captcha_image_url' => $captcha_image_url,
        ]);
    }
}
