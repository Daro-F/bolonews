<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_')]
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

    #[Route('/login', name: 'app_login')]
    public function login():Response
    {
        return $this->render('app/login.html.twig');
    }

    #[Route('/register', name: 'app_register')]
    public function register():Response
    {
        return $this->render('app/register.html.twig');
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('user/profil.html.twig');
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }


}
