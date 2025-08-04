<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();

        return $this->render('app/index.html.twig', [
        'articles' => $articles
        ]);
    }

    #[Route('/articles', name: 'app_articles')]
    public function articles():Response
    {
        return $this->render('app/articles.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact():Response
    {
        return $this->render('app/contact.html.twig');
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('user/profil.html.twig');
    }

    #[Route('/article/show/{id}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }


}
