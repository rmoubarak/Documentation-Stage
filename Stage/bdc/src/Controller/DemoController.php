<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\Structure;
use App\Form\ActualiteFichierType;
use App\Services\Captcha;
use App\Services\Chiffre;
use App\Services\Clamav;
use App\Services\EducationNationale;
use App\Services\Elise;
use App\Services\Organigramme;
use App\Services\Sirene;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/demo')]
#[IsGranted('ROLE_USER')]
class DemoController extends AbstractController
{
    #[Route('/', name: "demo_index", methods: ['GET', 'POST'])]
    public function index(
        Chiffre $chiffre,
        Captcha $captcha,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $chaine = "Belle chaîne à chiffrer !";
        $encrypt = $chiffre->encrypt($chaine);
        $decrypt = $chiffre->decrypt($encrypt);

        // Stats Captcha sur les 6 derniers mois
        $captcha_valides = [];
        $captcha_non_valides = [];
        $captcha_expires = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTimeImmutable();

            $start = $date->modify("midnight first day of -$i months");

            // Dernier jour du mois ou jour actuel
            if ($i == 0) {
                $end = $date->modify("today")->setTime(23, 59, 59);
            } else {
                $end = $date->modify("last day of -$i months")->setTime(23, 59, 59);
            }

            $captchaStats = $captcha->getStatistiques($start->format('Y-m-d'), $end->format('Y-m-d'));

            if ($captchaStats) {
                $captcha_valides[] = ['x' => $end->getTimestamp() * 1000, 'y' => $captchaStats->captcha_ok];
                $captcha_non_valides[] = ['x' => $end->getTimestamp() * 1000, 'y' => $captchaStats->captcha_ko];
                $captcha_expires[] = ['x' => $end->getTimestamp() * 1000, 'y' => $captchaStats->captcha_expired];
            }
        }

        // Clamav
        $actualite = $entityManager->getRepository(Actualite::class)->find(4);
        $form = $this->createForm(ActualiteFichierType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actualite->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'L\'actualité a été modifiée.');

            return $this->redirectToRoute('demo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demo/index.html.twig', [
            'chaine' => $chaine,
            'encrypt' => $encrypt,
            'decrypt' => $decrypt,
            'captcha_valides' => ($captcha_valides),
            'captcha_non_valides' => ($captcha_non_valides),
            'captcha_expires' => ($captcha_expires),
            'actualite' => $actualite,
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }

    #[Route('/elise', name: "demo_elise", methods: ['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function elise(Elise $elise): Response
    {
        $token = $elise->getUserTokenByLogin('AdminGED');
        $response = $elise->getMailsByContactId('CONTACTS_ORGANISMES_20859', $token);
        $elise->parseMailsByContactId($response);

        return $this->render('demo/elise.html.twig');
    }

    #[Route('/captcha', name: "demo_captcha", methods: ['GET', 'POST'])]
    public function captcha(Request $request, Captcha $captcha): Response
    {
        $headers = $request->headers;
        $infos = $captcha->getAntibotInfo($headers->get('User-Agent'), $headers->get('Host'));
        $check = null;

        if ($request->getMethod() == 'POST') {
            $token = $request->request->get('li-antibot-token');
            $check = $captcha->checkAntibotToken($token);
        }

        return $this->render('demo/captcha.html.twig', [
            'captcha_antibot_id' => $infos->antibotId,
            'captcha_request_id' => $infos->requestId,
            'captcha_sp_key' => $this->getParameter('captcha.sp_key'),
            'antibotResult' => $infos->antibotResult,
            'check' => $check,
        ]);
    }

    /**
     * Retourne un json d'événements
     */
    #[Route('/calendar', name: "demo_fullcalendar", options: ['expose' => true], methods: ['GET'])]
    public function calendar(Request $request): Response
    {
        $today = new \DateTime();

        $events = [[
            'id' => 1,
            'title' => 'TOTO',
            'start' => $today->format('Y-m-d'),
            'textColor' => '#FFFFFF',
        ], [
            'id' => 2,
            'title' => 'TATA',
            'start' => $today->modify('+1 day')->format('Y-m-d'),
            'textColor' => '#FFFFFF',
        ]
        ];

        return new Response(json_encode($events));
    }

    #[Route('/sirene', name: "demo_sirene", methods: ['GET', 'POST'])]
    public function sirene(Request $request, Sirene $sirene): Response
    {
        $etablissements['etablissements'] = [];
        $filtre_siren = $request->request->get('filtre_siren');
        $filtre_denomination = $request->request->get('filtre_denomination');
        $filtre_hdf = $request->request->get('filtre_hdf', 0);
        $filtre_limite = $request->request->get('filtre_limite', 20);

        if ($request->getMethod() == 'POST') {
            $etablissements = $sirene->searchEtablissement($filtre_siren, $filtre_denomination, $filtre_hdf == 1, $filtre_limite);
        }

        return $this->render('demo/sirene.html.twig', [
            'etablissements' => $etablissements ? $etablissements['etablissements'] : null,
            'filtre_siren' => $filtre_siren,
            'filtre_denomination' => $filtre_denomination,
            'filtre_hdf' => $filtre_hdf,
            'filtre_limite' => $filtre_limite,
        ]);
    }

    #[Route('/educationnationale', name: "demo_education_nationale", methods: ['GET', 'POST'])]
    public function educationNationale(Request $request, EducationNationale $educationNationale): Response
    {
        $filtre_q = $request->request->get('filtre_q', '');
        $filtre_type = $request->request->get('filtre_type', 'Collège');
        $filtre_statut = $request->request->get('filtre_statut', '');
        $filtre_limite = $request->request->get('filtre_limite', 20);

        $colleges = $educationNationale->findColleges($filtre_q, $filtre_type, strtoupper($filtre_statut), $filtre_limite, 0);

        return $this->render('demo/education_nationale.html.twig', [
            'colleges' => $colleges,
            'filtre_q' => $filtre_q,
            'filtre_type' => $filtre_type,
            'filtre_statut' => $filtre_statut,
            'filtre_limite' => $filtre_limite,
        ]);
    }
}
