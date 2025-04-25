<?php

namespace App\EventListener;

use App\Entity\LogArticle;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LogArticleListener
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();

// Vérifier que l'utilisateur est connecté
        if (!$user) {
            return;
        }


        $request = $event->getRequest();

// Vérifier si l'on consulte un article
        $route = $request->attributes->get('_route');
        if ($route !== 'article_show') {
            return;
        }

        $article = $request->attributes->get('article');
        if (!$article instanceof Article) {
            return;
        }


// Vérifier si l'utilisateur a déjà lu cet article récemment (ex: 5 minutes)
        $logRepo = $this->entityManager->getRepository(LogArticle::class);
        $lastLog = $logRepo->findOneBy([
            'article' => $article,
            'utilisateur' => $user
        ], ['date' => 'DESC']);

        if ($lastLog) {
            $now = new \DateTime();
            $interval = $now->getTimestamp() - $lastLog->getDate()->getTimestamp();

// Empêcher un enregistrement si la dernière lecture date de moins de 5 minutes
            if ($interval < 300) {
                return;
            }
        }

// Enregistrer la lecture
        $log = new LogArticle();
        $log->setUtilisateur($user);
        $log->setArticle($article);
        $log->setDate(new \DateTime());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
