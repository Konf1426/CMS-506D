<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('', name: 'comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();

        return $this->json($comments);
    }

    #[Route('/create', name: 'comment_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = json_decode($request->getContent(), true);

        $comment = new Comment();
        $comment->setContent($data['content']);
        $comment->setAuthor($this->getUser());

        $em->persist($comment);
        $em->flush();

        return $this->json($comment, Response::HTTP_CREATED);
    }

    #[Route('/{id}/edit', name: 'comment_edit', methods: ['PUT'])]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        if ($comment->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce commentaire.');
        }

        $data = json_decode($request->getContent(), true);
        $comment->setContent($data['content']);

        $em->flush();

        return $this->json($comment);
    }

    #[Route('/{id}', name: 'comment_delete', methods: ['DELETE'])]
    public function delete(Comment $comment, EntityManagerInterface $em): Response
    {
        if ($comment->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce commentaire.');
        }

        $em->remove($comment);
        $em->flush();

        return $this->json(['message' => 'Comment deleted'], Response::HTTP_NO_CONTENT);
    }
}
