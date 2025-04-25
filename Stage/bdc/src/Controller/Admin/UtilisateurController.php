<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\UtilisateurAdType;
use App\Form\UtilisateurType;
use App\Services\Agent;
use App\Services\SendEmail;
use App\Services\Excel;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateur')]
#[IsGranted('ROLE_ADMIN')]
class UtilisateurController extends AbstractController
{
    #[Route('/', name: "utilisateur_index", methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/utilisateur/index.html.twig');
    }

    #[Route('/list/{page<\d+>?}', name: "utilisateur_list", methods: ['GET', 'POST'])]
    public function list(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, int $page = 1): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $q = $request->get('q', '');

        $entities = $entityManager
            ->getRepository(Utilisateur::class)
            ->findByFilter($q);

        $utilisateurs = $paginator->paginate($entities, $page, Utilisateur::NUM_ITEMS);

        return $this->render('admin/utilisateur/_list.html.twig', [
            'utilisateurs' => $utilisateurs,
            'q' => $q,
        ]);
    }

    #[Route('/new', name: "utilisateur_new", methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendEmail $sendEmail): Response
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setCreatedAt(new \DateTime());

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $sendEmail->utilisateurNew($utilisateur);

            return new Response('ok');
        }

        return $this->render('admin/utilisateur/_new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/newad', name: "utilisateur_newad", methods: ['GET', 'POST'])]
    public function newAdAction(Request $request, EntityManagerInterface $entityManager, Agent $agent, SendEmail $sendEmail): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurAdType::class, $utilisateur);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            // On récupère le login de l'autocomplete
            $login = $form->get('adUserHidden')->getData();

            // On ne crée que s'il n'existe pas déjà en base
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->findOneByLogin($login);

            if (!$utilisateur) {
                $user = $agent->add($login);

                if ($user) {
                    $sendEmail->utilisateurNew($user);

                    return new Response('ok');
                } else {
                    return new Response("Utilisateur inconnu.");
                }
            } else {
                return new Response('Cet utilisateur est déjà dans la liste.');
            }
        }

        return $this->render('admin/utilisateur/_formAd.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affichage d'un select
     */
    #[Route('/select/{prefixe}', name: "utilisateur_select", methods: ['GET'])]
    public function select(Request $request, EntityManagerInterface $entityManager, string $prefixe): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $utilisateurs = $entityManager
            ->getRepository(Utilisateur::class)
            ->findBy([], ['nom' => 'ASC'])
        ;

        return $this->render('admin/utilisateur/_select.html.twig', [
            'utilisateurs' => $utilisateurs,
            'prefixe' => $prefixe,
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: "utilisateur_edit", methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return new Response('ok');
        }

        return $this->render('admin/utilisateur/_edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/excel', name: "utilisateur_excel", methods: ['GET'])]
    public function excel(EntityManagerInterface $entityManager, Excel $excel): Response
    {
        $utilisateurs = $entityManager
            ->getRepository(Utilisateur::class)
            ->findBy([], ['nom' => 'ASC']);

        $response =  new StreamedResponse(function () use ($excel, $utilisateurs) {
            $excel->utilisateurs($utilisateurs);
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="utilisateurs.xlsx"');
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    #[Route('/delete/{id<\d+>}', name: "utilisateur_delete", methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Utilisateur $utilisateur): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return new Response('Erreur : token non valide');
        }

        try {
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('Suppression impossible');
        }
    }
}
