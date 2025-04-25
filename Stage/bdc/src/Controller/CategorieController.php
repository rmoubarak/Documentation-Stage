<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[Route('/categorie')]
#[IsGranted("ROLE_ADMIN")]
class CategorieController extends AbstractController {

    #[Route('/', name: 'categorie_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig');
    }


    #[Route('/list/{page<\d+>?}', name: "categorie_list", methods: ['GET', 'POST'])]
    public function list(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, int $page = 1): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $entities = $entityManager
            ->getRepository(Categorie::class)
            ->findAll();

       $categories = $paginator->paginate($entities, $page, Categorie::NUM_ITEMS);

        return $this->render('categorie/_list.html.twig', [
           'categories' => $categories,
        ]);
    }



    #[Route('/new', name: "categorie_new", methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return new Response('ok');
        }

        return $this->render('categorie/_new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }


    #[Route('/edit/{id<\d+>}', name: "categorie_edit", methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager->flush();

            return new Response('ok');
        }

        return $this->render('categorie/_edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id<\d+>}', name: "categorie_delete", methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Categorie $categorie): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return new Response('Erreur : token non valide');
        }

        try {
            $entityManager->remove($categorie);
            $entityManager->flush();

            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('Suppression impossible');
        }
    }

}
