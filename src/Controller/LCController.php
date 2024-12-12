<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Form\LigneCommandeType;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/l/c')]
final class LCController extends AbstractController
{
    #[Route(name: 'app_l_c_index', methods: ['GET'])]
    public function index(LigneCommandeRepository $ligneCommandeRepository): Response
    {
        return $this->render('lc/index.html.twig', [
            'ligne_commandes' => $ligneCommandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_l_c_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligneCommande = new LigneCommande();
        $form = $this->createForm(LigneCommandeType::class, $ligneCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ligneCommande);
            $entityManager->flush();

            return $this->redirectToRoute('app_l_c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lc/new.html.twig', [
            'ligne_commande' => $ligneCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_l_c_show', methods: ['GET'])]
    public function show(LigneCommande $ligneCommande): Response
    {
        return $this->render('lc/show.html.twig', [
            'ligne_commande' => $ligneCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_l_c_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneCommande $ligneCommande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigneCommandeType::class, $ligneCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_l_c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lc/edit.html.twig', [
            'ligne_commande' => $ligneCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_l_c_delete', methods: ['POST'])]
    public function delete(Request $request, LigneCommande $ligneCommande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneCommande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ligneCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_l_c_index', [], Response::HTTP_SEE_OTHER);
    }
}
