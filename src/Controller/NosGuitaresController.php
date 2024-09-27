<?php

namespace App\Controller;

use App\Entity\NosGuitares;
use App\Form\NosGuitaresType;
use App\Repository\NosGuitaresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nos/guitares')]
final class NosGuitaresController extends AbstractController
{
    #[Route(name: 'app_nos_guitares_index', methods: ['GET'])]
    public function index(NosGuitaresRepository $nosGuitaresRepository): Response
    {
        return $this->render('nos_guitares/index.html.twig', [
            'nos_guitares' => $nosGuitaresRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_nos_guitares_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nosGuitare = new NosGuitares();
        $form = $this->createForm(NosGuitaresType::class, $nosGuitare);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Génération d'un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacement du fichier dans le répertoire des images
                $imageFile->move(
                    $this->getParameter('images_directory'), // Défini ce paramètre dans le fichier de configuration
                    $newFilename
                );

                // Mise à jour de l'entité avec le nom de fichier
                $nosGuitare->setImage($newFilename);
            }

            $entityManager->persist($nosGuitare);
            $entityManager->flush();

            return $this->redirectToRoute('app_nos_guitares_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nos_guitares/new.html.twig', [
            'nos_guitare' => $nosGuitare,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nos_guitares_show', methods: ['GET'])]
    public function show(NosGuitares $nosGuitare): Response
    {
        return $this->render('nos_guitares/show.html.twig', [
            'nos_guitare' => $nosGuitare,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_nos_guitares_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NosGuitares $nosGuitare, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NosGuitaresType::class, $nosGuitare);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nos_guitares_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nos_guitares/edit.html.twig', [
            'nos_guitare' => $nosGuitare,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nos_guitares_delete', methods: ['POST'])]
    public function delete(Request $request, NosGuitares $nosGuitare, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $nosGuitare->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($nosGuitare);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nos_guitares_index', [], Response::HTTP_SEE_OTHER);
    }
}
