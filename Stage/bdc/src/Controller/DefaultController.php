<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\Structure;
use App\Services\Ldap;
use App\Services\Organigramme;
use App\Services\Sig;
use App\Services\Sirene;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    #[Route('/{actualite_id<\d+>?}', name: "default_index", methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        EntityManagerInterface $entityManager,
        #[MapEntity(mapping: ['actualite_id' => 'id'])] ?Actualite $actualite
    ): Response
    {
        // Récup des actus
        $actualites = $entityManager->getRepository(Actualite::class)->findBy(['statut' => Actualite::STATUTS[0]], ['createdAt' => 'DESC']);

        // Si l'actu n'est pas fournie, on récupère la dernière
        if ($actualite) {
            $actualite_id = $actualite->getId();
        } else if (count($actualites) > 0) {
            $actualite = $actualites[0];
            $actualite_id = $actualite->getId();
        } else {
            $actualite_id = 0;
        }

        // Récup des liens suivant et précédent
        $actualite_nav['total'] = count($actualites);
        foreach ($actualites as $i => $entity) {
            if ($entity->getId() == $actualite_id) {
                $actualite_nav['previous'] = isset($actualites[$i - 1]) ? $actualites[$i - 1]->getId() : null;
                $actualite_nav['next'] = isset($actualites[$i + 1]) ? $actualites[$i + 1]->getId() : null;
                $actualite_nav['position'] = $i + 1;
            }
        }

        return $this->render('default/index.html.twig', [
            'actualite' => $actualite,
            'actualite_nav' => $actualite_nav,
        ]);
    }

    #[Route('/version', name: "default_version", methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function version(): Response
    {
        return $this->render('default/version.html.twig');
    }

    #[Route('/failed', name: "default_failed", methods: ['GET'])]
    public function failed(): Response
    {
        return $this->render('default/failed.html.twig');
    }

    #[Route('/logout', name: "default_logout", methods: ['GET'])]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();

        $response = new Response();
        $response->headers->clearCookie('PHPSESSID');

        return $this->render('default/logout.html.twig', [], $response);
    }

    #[Route('/profil', name: "default_profil", methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('default/profil.html.twig');
    }

    /**
     * Retourne un tableau d'utilisateurs AD avec nom et login en fonction du nom
     */
    #[Route('/ldapsearch/{q}', name: "ldap_search", options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function ldapSearch(Ldap $ldap, string $q = ''): Response
    {
        $users = $ldap->findUsersByName($q);
        $return = [];

        foreach ($users as $i => $user) {
            if ($ldap->getNom($user)) {
                $return[$i]['civilite'] = $ldap->getCivilite($user);
                $return[$i]['value'] = $ldap->getNom($user) . ' ' . $ldap->getPrenom($user);
                $return[$i]['login'] = $ldap->getLogin($user);
                $return[$i]['fonction'] = $ldap->getFonction($user);
                $return[$i]['sigle'] = $ldap->getDirectionSigle($user);
            }
        }

        // Tri sur le nom
        foreach ($return as $i => $row) {
            $value[$i] = $row['value'];
            $login[$i] = $row['login'];
            $fonction[$i] = $row['fonction'];
        }
        if ($return) {
            array_multisort($value, SORT_ASC, $login, SORT_ASC, $return);
        }

        return new JsonResponse($return);
    }

    /**
     * Retourne un json pour autocomplétion d'adresse postale
     */
    #[Route('/adressepostale/{q}/{code_insee?}', name: "adresse_postale", options: ['expose' => true], defaults: ['code_insee' => ''], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function adressePostale(Sig $sig, string $q, string $code_insee = ''): Response
    {
        return new JsonResponse($sig->findAdresses($q, $code_insee));
    }

    /**
     * Retourne un json pour autocomplétion de communes belges
     */
    #[Route('/commune/belge/{q}', name: "commune_belge", options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function communeBelge(Sig $sig, string $q): Response
    {
        $communes = $sig->findCommunesBelges($q);

        return new JsonResponse($sig->formatCommuneBelgeResponse($communes));
    }

    #[Route('/calculdistance', name: "default_calculdistance", options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function calculDistance(Request $request, Sig $sig): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        return new Response($sig->calculItineraire($request->get('startX'), $request->get('startY'),
            $request->get('endX'), $request->get('endY')));
    }

    #[Route('/sirene/{denomination}', name: "default_sirene", options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function sirene(Sirene $sirene, string $denomination): Response
    {
        $etablissements = $sirene->searchEtablissement('', $denomination, 0, 200);

        $response = [];
        foreach ($etablissements['etablissements'] as $key => $etablissement) {
            $adresse = $etablissement['adresseEtablissement'];

            $response[$key]['denomination'] = $etablissement['uniteLegale']['denominationUniteLegale'];
            $response[$key]['siret'] = $etablissement['siret'];
            $response[$key]['siren'] = $etablissement['siren'];
            $response[$key]['adresse'] = $adresse['numeroVoieEtablissement'] . ' ' . $adresse['typeVoieEtablissement'] . ' ' . $adresse['libelleVoieEtablissement'];
            $response[$key]['commune'] = $adresse['libelleCommuneEtablissement'];
            $response[$key]['codepostal'] = $adresse['codePostalEtablissement'];
        }

        return new JsonResponse($response);
    }
}
