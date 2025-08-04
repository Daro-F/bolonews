<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('contact/index.html.twig');
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

    #[Route('/list', name: 'app_list')]
    public function list(Request $request, ArticleRepository $articleRepository)
    {
        // Création du formulaire de recherche
        $form = $this->createForm(SearchType::class);
        
        // Traitement de la requête
        $form->handleRequest($request);
       
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère la donnée du champ 'q'
            $data = $form->getData();
            $query = $data['q'];

            // Si le champ n'est pas vide, on effectue la recherche
            if (!empty($query)) {
                $articles = $articleRepository->searchByTerm($query);
            }
        } else {
            $articles = $articleRepository->findAll();
        }

        // On renvoie la vue avec les articles (filtrés ou non) et le formulaire
        return $this->render('app/list.html.twig', [
            'articles' => $articles,
            'form' => $form->createView(),
        ]);
    }
}
