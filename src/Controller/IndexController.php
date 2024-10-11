<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ArticleType;

class IndexController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Injection de l'EntityManager via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'homepage')]
    public function home(): Response
    {
        
        $articles = $this->entityManager->getRepository(Article::class)->findAll();

       
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    #[Route('/article/save', name: 'article_save', methods: ['GET'])]
    public function save(): Response
    {
        
        $article = new Article();
        $article->setNom('Article 1');
        $article->setPrix(1000);

        // Sauvegarde de l'article
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new Response('Article enregistré avec id ' . $article->getId());
    }

    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('homepage'); // Change to 'homepage' or the correct route
        }

        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        // Récupérer l'article par ID
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Rendre la vue avec l'article
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'edit_article', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
{
    // Récupérer l'article à modifier
    $article = $this->entityManager->getRepository(Article::class)->find($id);

    // Vérifier si l'article existe
    if (!$article) {
        throw $this->createNotFoundException('Article non trouvé');
    }

    // Création du formulaire pour modifier l'article
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    // Gestion de la soumission du formulaire
    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->flush(); // Sauvegarde des modifications

        // Redirection après la modification de l'article
        return $this->redirectToRoute('homepage'); // Redirige vers la page d'accueil ou à la liste des articles
    }
        // Rendre la vue avec le formulaire
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article', methods: ['DELETE'])]
    public function delete(Request $request, int $id): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        // Redirection après la suppression de l'article
        return $this->redirectToRoute('homepage'); // Redirige vers la page d'accueil
    }
}
