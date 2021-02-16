<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends AbstractController
{
    /**
     * Currently unused: just showing a controller with a constructor!
     */
    private $isDebug;

    public function __construct(bool $isDebug)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * @Route("/", name="app_homepage")
     * @param ArticleRepository $repository
     * @return Response
     */
    public function homepage(ArticleRepository $repository)
    {
        $articles = $repository->findAllPublishedOrderedByNewest();

        return $this->render('article/homepage.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_show")
     * @param Article $article
     * @return Response
     */
    public function show(Article $article)
    {

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'likes' => [
                'count' => $article->getLikes()->count(),
                'check' => $article->getLikes()->contains($this->getUser())
                ]
        ]);
    }

    /**
     * @Route("/article/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     * @param Article $article
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function toggleArticleHeart(Article $article, LoggerInterface $logger, EntityManagerInterface $em)
    {
        /* @var User $user */
        $user = $this->getUser();
        $likeCollection = $article->getLikes();
        $count = $likeCollection->count();

        $isLiked = $likeCollection->contains($user);
        if (!$isLiked) {
            $user->addArticleLike($article);
            $count++;
        } else{
            $user->removeArticleLike($article);
            $count--;
        }
        $em->flush();

        $logger->info('Article is being hearted!');

        return new JsonResponse(['hearts' => $count]);
    }
}
