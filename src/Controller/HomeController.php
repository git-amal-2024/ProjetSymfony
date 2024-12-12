<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $articles = [['id' => 1, 'nom' => 'Produit 1', 'description' => 'Description 1', 'prix' => 100, 'image' => 'path/to/image1.jpg'],
            ['id' => 2, 'nom' => 'Produit 2', 'description' => 'Description 2', 'prix' => 200, 'image' => 'path/to/image2.jpg'],
        ];

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
