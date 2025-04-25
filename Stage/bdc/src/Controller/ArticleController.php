<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\LogArticle;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/article')]
#[IsGranted("ROLE_ADMIN")]
class ArticleController extends AbstractController
{

    #[Route('/', name: 'article_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('article/index.html.twig');
    }



    #[Route('/list/{page<\d+>?}', name: "article_list", methods: ['GET', 'POST'])]
    public function list(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, int $page = 1): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $q = $request->get('q', '');

        $entities = $entityManager
            ->getRepository(Article::class)
            ->findByFilter($q);

        $articles = $paginator->paginate($entities, $page, Article::NUM_ITEMS);

        return $this->render('article/_list.html.twig', [
            'articles' => $articles,
            'q' => $q,
        ]);
    }


    #[Route('/new', name: "article_new", methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setCreatedAt(new \DateTime());
        $article->setUtilisateur($this->getUser());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);

            $logArticle = new LogArticle();
            $logArticle->setArticle($article);
            $logArticle->setUtilisateur($this->getUser());
            $logArticle->setDate(new \DateTime());

            $entityManager->persist($logArticle);

            $entityManager->flush();

            return new Response('ok');
        }

        return $this->render('article/_new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }


    #[Route('/edit/{id<\d+>}', name: "article_edit", methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            // Rediriger vers la liste des articles après modification
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id<\d+>}', name: "article_delete", methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Article $article): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return new Response('Erreur : token non valide');
        }

        try {
            $entityManager->remove($article);
            $entityManager->flush();

            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('Suppression impossible');
        }
    }


    #[Route('/article/{id}', name: 'article_log')]
    public function log(Article $article): Response
    {
        return $this->render('article/_log.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/show/{id}', name: 'article_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }


}




