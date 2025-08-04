<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchType;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


final class ArticleController extends AbstractController
{
    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /* On crée un nouvel objet 'Article' vide pour l'instant.
        On crée un formulaire en utilisant 'ArticleType' en le reliant a cet article vide.
        Ensuite il faut récupérer les données du formulaire en utilisant (handleRequest($request)) pour ça

            (Si la request est en POST et qu'elle contient des données valide, elles remplissent '$article')

        On vérifie donc que le formulaire a été soumis et est valide :
            Si oui :
                Doctrine doit enregistrer l'article ('persist')
                Puis on enregistre en base ('flush')
            Sinon :
                Renvoie a la page du formulaire

        */
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gère l'erreur proprement (log ou message flash)
                }

                $article->setImage($newFilename);
            }

            if (!$this->getUser()) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer un article.');
            }

            $article->setAuteur($this->getUser());
            $article->setCreatedAt(new \DateTime());

            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/articles/list', name: 'article_index')]
    public function index(Request $request, ArticleRepository $articleRepository,)
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
        return $this->render('app/index.html.twig', [
            'articles' => $articles,
            'form' => $form,
        ]);
    }

    #[Route('/article/show/{id}', name: 'app_article_show')]
    public function show(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager): Response {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour commenter.');
            }

            $commentaire->setAuteur($this->getUser());
            $commentaire->setArticle($article);
            $commentaire->setCreatedAt(new \DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/article/edit/{id}', name: 'app_article_edit')]
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Vérifie que l'utilisateur peut modifier l'article (ex : auteur ou admin)
        if ($article->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet article.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $article->setImage($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès.');
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

}
