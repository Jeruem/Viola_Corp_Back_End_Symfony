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
use OpenApi\Attributes as OA;


#[Route('/api/viola', name: 'app_api_viola_')]

class ViolaController extends AbstractController
{

    public function __construct(private EntityManagerInterface $manager, private ViolaRepository $repository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }
    #[Route(methods: 'POST')]

    #[OA\Post(
        path: "/api/viola",
        summary: "Créer un magasin",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du magasin à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Nom du magasin"),
                    new OA\Property(property: "description", type: "string", example: "Description du magasin"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Magasin créé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Nom du magasin"),
                        new OA\Property(property: "description", type: "string", example: "Description du magasin"),
                        new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    ]
                )
            )
        ]
    )]
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

    #[OA\Get(
        path: "/api/viola/{id}",
        summary: "Afficher un magasin par ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du magasin à afficher",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "magasin trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Nom du magasin"),
                        new OA\Property(property: "description", type: "string", example: "Description du magasin"),
                        new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "magasin non trouvé"
            )
        ]
    )]
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

    #[OA\Put(
        path: "/api/viola/{id}",
        summary: "Modifier un magasin par ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du magasin à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Nouvelles données du magasin à mettre à jour",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Nouveau nom du magasin"),
                    new OA\Property(property: "description", type: "string", example: "Nouvelle description du magasin"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Magasin modifié avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Magasin non trouvé"
            )
        ]
    )]
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

    #[OA\Delete(
        path: "/api/viola/{id}",
        summary: "Supprimer un magasin par ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du magasin à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Magasin supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Magasin non trouvé"
            )
        ]
    )]
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


