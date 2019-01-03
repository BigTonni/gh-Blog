<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController.
 */
class CommentController extends AbstractController
{
    /**
     * @param Request $request
     * @param Post    $post
     *
     * @return Response
     *
     * @Route("/comment/{slug}/new", methods={"POST"}, name="comment_create")
     */
    public function new(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $comment = new Comment();
        $post->addComment($comment);
        $comment->setAuthor($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create New Comment',
            'post' => $post,
        ]);
    }
}