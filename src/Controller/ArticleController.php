<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/article')]
final class ArticleController extends AbstractController
{   #[Route('/list', name: 'app_products', methods: ['GET'])]
    public function listProducts(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll(); // Récupère tous les articles
        dump($articles);
        return $this->render( 'article/article.html.twig', [
            'articles' => $articles,
        ]);
    }


    #[Route(name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {   // Vérifier si l'utilisateur a le rôle d'administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
    #[Route('/admin/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**  @var UploadedFile $imageFile */
            $imageFile=$form->get('image')->getData();
            if ($imageFile){
                $filename=pathinfo($imageFile->getClientOriginalNAme(),PATHINFO_FILENAME);
                //$originalname= $slugger->slug($filename);
                $newFilename = $filename.'-'.uniqid().'-'.$imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('image_directory'), $newFilename);
                }
                catch(FileException $e)
                {//...exception

                }

                $article->setImage($newFilename);
            }
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('succes','Article ajouté avec succès');

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**  @var UploadedFile $imageFile */
            $imageFile=$form->get('image')->getData();
            if ($imageFile){
                $filename=pathinfo($imageFile->getClientOriginalNAme(),PATHINFO_FILENAME);
                //$originalname= $slugger->slug($filename);
                $newFilename = $filename.'-'.uniqid().'-'.$imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('image_directory'), $newFilename);
                }
                catch(FileException $e)
                {//...exception

                }

                $article->setImage($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('info','L article a été modifié');

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('danger','Cet article a été supprimé');
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
