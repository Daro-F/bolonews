<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticleRepository;

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
}
