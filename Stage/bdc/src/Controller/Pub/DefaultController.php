<?php

namespace App\Controller\Pub;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/public')]
#[IsGranted('ROLE_PUBLIC')]
class DefaultController extends AbstractController
{
    #[Route('/', name: "public_default_index", methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('public/default/index.html.twig');
    }
}
