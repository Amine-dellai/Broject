<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les articles de la base de données
        $articles = $entityManager->getRepository(Article::class)->findAll();

        // Rendre la vue avec les articles récupérés
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/save', name: 'article_save', methods: ['GET'])]
    public function save(EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouvel article
        $article = new Article();
        $article->setNom('Article 1');
        $article->setPrix(1000);

        // Persist et flush l'article dans la base de données
        $entityManager->persist($article);
        $entityManager->flush();

        // Retourner une réponse avec l'ID de l'article enregistré
        return new Response('Article enregistré avec id ' . $article->getId());
    }

    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();

        // Créer le formulaire
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer'])
            ->getForm();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $article = $form->getData();

            // Persister l'article et le sauvegarder en base de données
            $entityManager->persist($article);
            $entityManager->flush();

            // Rediriger vers la liste des articles après création
            return $this->redirectToRoute('app_home');
        }

        // Rendre la vue pour afficher le formulaire
        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        // Rendre la vue pour afficher un article spécifique
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'edit_article', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de persister, car l'article est déjà géré par Doctrine
            $entityManager->flush();

            // Rediriger vers la liste des articles après modification
            return $this->redirectToRoute('app_home');
        }

        // Rendre la vue pour afficher le formulaire d'édition
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article', methods: ['DELETE'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Supprimer l'article
        $entityManager->remove($article);
        $entityManager->flush();

        // Rediriger vers la liste des articles après suppression
        return $this->redirectToRoute('app_home');
    }
}
