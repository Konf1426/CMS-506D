<?php

namespace App\Controller;

use App\Entity\Content;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/content')]
class ContentController extends AbstractController
{
    #[Route('', name: 'content_index', methods: ['GET'])]
    public function index(ContentRepository $contentRepository): Response
    {
        // Liste tous les contenus
        $contents = $contentRepository->findAll();

        return $this->json($contents);
    }

    #[Route('/create', name: 'content_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $content = new Content();
        $content->setTitle($data['title']);
        $content->setMetaTitle($data['metaTitle'] ?? null);
        $content->setMetaDescription($data['metaDescription'] ?? null);
        $content->setBody($data['body']);
        $content->setSlug($data['slug']);
        $content->setTags($data['tags'] ?? []);
        $content->setCoverImage($data['coverImage'] ?? null);
        $content->setCoverImageFile($data['coverImageFile'] ?? null);

        $em->persist($content);
        $em->flush();

        return $this->json($content, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'content_show', methods: ['GET'])]
    public function show(Content $content): Response
    {
        return $this->json($content);
    }

    #[Route('/{id}/edit', name: 'content_edit', methods: ['PUT'])]
    public function edit(Content $content, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $content->setTitle($data['title']);
        $content->setMetaTitle($data['metaTitle'] ?? null);
        $content->setMetaDescription($data['metaDescription'] ?? null);
        $content->setBody($data['body']);
        $content->setSlug($data['slug']);
        $content->setTags($data['tags'] ?? []);
        $content->setCoverImage($data['coverImage'] ?? null);
        $content->setCoverImageFile($data['coverImageFile'] ?? null);

        $em->flush();

        return $this->json($content);
    }

    #[Route('/{id}', name: 'content_delete', methods: ['DELETE'])]
    public function delete(Content $content, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($content);
        $em->flush();

        return $this->json(['message' => 'Content deleted'], Response::HTTP_NO_CONTENT);
    }
}
