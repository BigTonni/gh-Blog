<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Like;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class LikeController.
 */
class LikeController extends AbstractController
{
    /**
     * @param Post $post
     *
     * @return Response
     *
     * @IsGranted("ROLE_USER")
     * @Route("/{_locale}/post/like/{slug}", defaults={"_locale": "en"}, name="post_like")
     */
    public function like(Post $post): Response
    {
        $em = $this->getDoctrine()->getManager();
        $likes = $post->getLikes();

        if ($likes->isEmpty()) {
            $like = new Like();
            $like->setUser($this->getUser());
            $post->addLike($like);

            $em->persist($post);
        } else {
            $isDeleted = false;
            foreach ($likes as $like) {
                if ($like->getUser() === $this->getUser()) {
                    $post->removeLike($like);
                    $em->persist($post);

                    $isDeleted = true;
                }
            }

            if (!$isDeleted) {
                $like = new Like();
                $like->setUser($this->getUser());
                $post->addLike($like);

                $em->persist($post);
            }
        }

        $em->flush();

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }
}
