<?php

namespace App\Controller;

use App\Entity\Viola;
use App\Repository\ViolaRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/viola', name: 'app_api_viola_')]

class ViolaController extends AbstractController
{

    public function __construct(private EntityManagerInterface $manager, private ViolaRepository $repository)
    {
    }
    #[Route(methods: 'POST')]
    public function new(): Response
    {
        $viola = new Viola();
        $viola->setName('Viola Corp');
        $viola->setDescription('Ce savoir-faire inégalé dans la fabrication de nos instruments');
        $viola->setCreatedAt(new DateTimeImmutable());
        // Tell Doctrine you want to (eventually) save the restaurant (no queries yet)
        $this->manager->persist($viola);
        // Actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();
        return $this->json(
            ['message' => "Viola resource created with {$viola->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): Response
    {
        $viola = $this->repository->findOneBy(['id' => $id]);
        if (!$viola) {
            throw $this->createNotFoundException("No shop found for {$id} id");
        }
        return $this->json(
            ['message' => "A Shop was found : {$viola->getName()} for {$viola->getId()} id"]
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id): Response
    {
        $viola = $this->repository->findOneBy(['id' => $id]);
        if (!$viola) {
            throw $this->createNotFoundException("No Shop found for {$id} id");
        }
        $viola->setName('Shop name updated');
        $this->manager->flush();
        return $this->redirectToRoute('app_api_viola_show', ['id' => $viola->getId()]);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $viola = $this->repository->findOneBy(['id' => $id]);
        if (!$viola) {
            throw $this->createNotFoundException("No Shop found for {$id} id");
        }
        $this->manager->remove($viola);
        $this->manager->flush();
        return $this->json(['message' => "Shop resource deleted"], Response::HTTP_NO_CONTENT);
    }

}


