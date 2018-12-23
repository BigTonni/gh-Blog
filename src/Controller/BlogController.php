<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="posts_show")
     */
    public function showPosts(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository(Post::class)->createQueryBuilder('p')->getQuery();
        $posts = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('blog/post/show_posts.html.twig', [
            'title' => 'Show Posts',
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/show/{slug}", name="post_show")
     */
    public function showPost(Post $post)
    {
        $categories = $post->getCategory();

        return $this->render('blog/post/show_post.html.twig', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/{slug}", name="posts_in_category_show")
     */
    public function showPostsInCategory(Request $request, PaginatorInterface $paginator, Category $category)
    {
        $posts = $paginator->paginate($category->getPosts(), $request->query->getInt('page', 1));

        return $this->render('blog/post/show_posts.html.twig', [
            'title' => 'Show Post in Category '.$category->getName(),
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/create", name="post_create")
     */
    public function createPost(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('post_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/post/create_post.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create New Post',
        ]);
    }

    /**
     * @Route("/comment/{id}/new", methods={"POST"}, name="comment_create")
     * @ParamConverter("id", class="App\Entity\Post")
     */
    public function createComment(Request $request, Post $post): Response
    {
        $comment = new Comment();
        $comment->setPublishedAt(new \DateTime());
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comment/create_comment.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create New Comment',
        ]);
    }

    public function commentForm(Post $post): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/comment/create_comment.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
