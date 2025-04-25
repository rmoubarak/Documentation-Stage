<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/admin/actualite')]
#[IsGranted("ROLE_ADMIN")]
class ActualiteController extends AbstractController
{
    #[Route('/', name: 'actualite_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $actualites = $entityManager->getRepository(Actualite::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/actualite/index.html.twig', [
            'actualites' => $actualites,
        ]);
    }

    #[Route('/list/{page<\d+>?}', name: "actualite_list", methods: ['GET', 'POST'])]
    public function list(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, int $page = 1): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $q = $request->get('q', '');

        $entities = $entityManager
            ->getRepository(Actualite::class)
            ->findByFilter($q);

        $actualites = $paginator->paginate($entities, $page, Actualite::NUM_ITEMS);

        return $this->render('admin/actualite/_list.html.twig', [
            'actualites' => $actualites,
            'q' => $q,
        ]);
    }

    #[Route('/new', name: 'actualite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        $actualite = new Actualite();
        $actualite->setCreatedAt(new \DateTime());
        $actualite->setUtilisateur($this->getUser());
        $actualite->setStatut(Actualite::STATUTS[0]);

        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($actualite);
            $entityManager->flush();

            return new Response('ok');
        }

        return $this->render('admin/actualite/_new.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'actualite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Actualite $actualite, EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return new Response('ok');
        }

        return $this->render('admin/actualite/_edit.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: "actualite_delete", methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Actualite $actualite): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return new Response('Erreur : token non valide');
        }

        try {
            $entityManager->remove($actualite);
            $entityManager->flush();

            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('Suppression impossible');
        }
    }

    #[Route('/download/{id<\d+>}', name: 'actualite_download', methods: ['GET'])]
    public function download(Actualite $actualite, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($actualite, 'file', null, null, false);
    }
}
