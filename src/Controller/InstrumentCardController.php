<?php

// src/Controller/InstrumentCardController.php

namespace App\Controller;

use App\Repository\NosGuitaresRepository;
use App\Repository\NosbassesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InstrumentCardController extends AbstractController
{
    private $guitarRepository;
    private $bassRepository;

    public function __construct(NosGuitaresRepository $guitarRepository, NosbassesRepository $bassRepository)
    {
        $this->guitarRepository = $guitarRepository;
        $this->bassRepository = $bassRepository;
    }

    #[Route('/api/guitars', name: 'api_guitars')]
    public function getGuitars(): JsonResponse
    {
        $guitars = $this->guitarRepository->findAll();
        // Générer l'URL complète pour chaque image
        foreach ($guitars as $guitar) {
            $guitar->imageUrl = 'http://127.0.0.1:8000/uploads/images/' . $guitar->getImage(); // Assurez-vous d'utiliser getImage()
        }
        return $this->json($guitars);
    }


    #[Route('/api/basses', name: 'api_basses')]
    public function getBasses(): JsonResponse
    {
        $basses = $this->bassRepository->findAll();

        // Générer l'URL complète pour chaque image
        foreach ($basses as $bass) {
            $bass->imageUrl = 'http://127.0.0.1:8000/uploads/images/' . $bass->getImage(); // Assurez-vous d'utiliser getImage()
        }

        return $this->json($basses);
    }
}
