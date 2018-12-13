<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="posts_show")
     */
    public function showPosts()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository(Post::class)->findAll();

        return $this->render('blog/showPosts.html.twig', [
            'posts' => $posts,
            'title' => 'Show Posts',
        ]);
    }

    /**
     * @Route("/post/show/{id}", name="post_show", requirements={"page"="\d+"})
     */
    public function showPost($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository(Post::class)->find($id);
        $categories = $post->getCategory();

        return $this->render('blog/showPost.html.twig', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/{id}", name="posts_in_category_show", requirements={"page"="\d+"})
     */
    public function showPostInCategory($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($id);

        $posts = $category->getPosts();

        return $this->render('blog/showPosts.html.twig', [
            'posts' => $posts,
            'title' => 'Show Post in Category '.$category->getName(),
        ]);
    }
}
