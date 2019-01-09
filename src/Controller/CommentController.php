<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     * @IsGranted("ROLE_USER")
     * @Route("/{_locale}/comment/{slug}/new", defaults={"_locale": "en"}, methods={"POST"}, name="comment_create")
     */
    public function new(Request $request, Post $post): Response
    {
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
            'post' => $post,
        ]);
    }
}
