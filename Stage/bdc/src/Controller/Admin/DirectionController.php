<?php

namespace App\Controller\Admin;

use App\Entity\Pole;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/direction')]
#[IsGranted("ROLE_ADMIN")]
class DirectionController extends AbstractController
{
    #[Route('/select/{pole_id}/{utilisateur_id}', name: 'direction_select', options: ['expose' => true], methods: ['GET'])]
    public function select(
        Request $request,
        #[MapEntity(mapping: ['pole_id' => 'id'])] ?Pole $pole,
        #[MapEntity(mapping: ['utilisateur_id' => 'id'])] ?Utilisateur $utilisateur
    ): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('admin/direction/_select.html.twig', [
            'directions' => $pole?->getActivesDirections($utilisateur),
        ]);
    }
}
