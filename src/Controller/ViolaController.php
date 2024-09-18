<?php

namespace App\Controller;

use App\Entity\Viola;
use App\Repository\ViolaRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/api/viola', name: 'app_api_viola_')]

class ViolaController extends AbstractController
{

    public function __construct(private EntityManagerInterface $manager, private ViolaRepository $repository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $viola = $this->serializer->deserialize($request->getContent(), Viola::class, 'json');
        $viola->setCreatedAt(new DateTimeImmutable());
        $this->manager->persist($viola);
        $this->manager->flush();
        $responseData = $this->serializer->serialize($viola, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_viola_show',
            ['id' => $viola->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $viola = $this->repository->findOneBy(['id' => $id]);
        if ($viola) {
            $responseData = $this->serializer->serialize($viola, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $viola = $this->repository->findOneBy(['id' => $id]);
        if ($viola) {
            $viola = $this->serializer->deserialize(
                $request->getContent(),
                Viola::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $viola]
            );
            $viola->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
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


