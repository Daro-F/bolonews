<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(Request $request, ArticleRepository $articleRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Création et traitement du formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Articles de l'utilisateur
        $articlesPublies = $user->getArticles()->filter(function ($article) {
            return $article->isPublished();
        });

        $articlesNonPublies = $user->getArticles()->filter(function ($article) {
            return !$article->isPublished();
        });

        // Si recherche soumise
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('q')->getData();

            if (!empty($query)) {
                // Recherche uniquement dans les articles de l'utilisateur
                $articlesPublies = $articleRepo->searchByTerm($query, $user);

                // Ne garder que les articles publiés
                $articlesPublies = array_filter($articlesPublies, function ($article) {
                    return $article->isPublished();
                });
            }
        }

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'articlesPublies' => $articlesPublies,
            'articlesNonPublies' => $articlesNonPublies,
            'form' => $form->createView(),
        ]);
    }
}
