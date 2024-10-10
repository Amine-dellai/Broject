<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home')] // Utilisation des attributs PHP pour la route
    public function home(): Response
    {
        // Définir les articles
        $articles = ['Article 1', 'Article 2', 'Article 3'];

        // Appel de la méthode render avec une syntaxe correcte
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }
}
