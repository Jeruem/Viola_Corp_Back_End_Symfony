<?php

namespace App\Controller;

use App\Entity\Nosbasses;
use App\Form\NosbassesType;
use App\Repository\NosbassesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/nosbasses')]
final class NosbassesController extends AbstractController
{
    #[Route(name: 'app_nosbasses_index', methods: ['GET'])]
    public function index(NosbassesRepository $nosbassesRepository): Response
    {
        return $this->render('nosbasses/index.html.twig', [
            'nosbasses' => $nosbassesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_nosbasses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $nosbass = new Nosbasses();
        $form = $this->createForm(NosbassesType::class, $nosbass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier d'image uploadé
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire configuré pour stocker les images
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),  // Ce paramètre doit être configuré dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si l'upload échoue
                }

                // Stocker le nouveau nom de fichier dans l'entité Nosbasses
                $nosbass->setImage($newFilename);
            }

            $entityManager->persist($nosbass);
            $entityManager->flush();

            return $this->redirectToRoute('app_nosbasses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nosbasses/new.html.twig', [
            'nosbass' => $nosbass,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nosbasses_show', methods: ['GET'])]
    public function show(Nosbasses $nosbass): Response
    {
        return $this->render('nosbasses/show.html.twig', [
            'nosbass' => $nosbass,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nosbasses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nosbasses $nosbass, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(NosbassesType::class, $nosbass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier d'image uploadé
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire configuré pour stocker les images
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si l'upload échoue
                }

                // Stocker le nouveau nom de fichier dans l'entité Nosbasses
                $nosbass->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_nosbasses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nosbasses/edit.html.twig', [
            'nosbass' => $nosbass,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nosbasses_delete', methods: ['POST'])]
    public function delete(Request $request, Nosbasses $nosbass, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $nosbass->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($nosbass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nosbasses_index', [], Response::HTTP_SEE_OTHER);
    }
}
