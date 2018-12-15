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

        $postsRepository = $em->getRepository(Post::class);
        $posts = $postsRepository->findAll();

        $query = $postsRepository->createQueryBuilder('p')->getQuery();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('blog/post/showPosts.html.twig', [
            'title' => 'Show Posts',
            'posts' => $posts,
            'pagination' => $pagination,
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

        return $this->render('blog/post/showPost.html.twig', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/{id}", name="posts_in_category_show", requirements={"page"="\d+"})
     */
    public function showPostsInCategory(Request $request, PaginatorInterface $paginator, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $categoryRepository = $em->getRepository(Category::class);

        $category = $categoryRepository->find($id);
        $posts = $category->getPosts();

        $pagination = $paginator->paginate($posts, $request->query->getInt('page', 1));

        return $this->render('blog/post/showPosts.html.twig', [
            'posts' => $posts,
            'title' => 'Show Post in Category '.$category->getName(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/post/create", name="post_create")
     */
    public function createPost(Request $request)
    {
        $post = new Post();
        $post->setPublishedAt(new \DateTime());

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('post_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $link = $this->generateUrl('post_show', [
                'id' => $post->getId(),
            ]);

            return $this->redirect($link);
        }

        return $this->render('blog/post/createPost.html.twig', [
            'createPost_form' => $form->createView(),
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

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('blog/comment/createComment.html.twig', [
            'createComment_form' => $form->createView(),
            'title' => 'Create New Comment',
        ]);
    }

    public function commentForm(Post $post): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/comment/createComment.html.twig', [
            'post' => $post,
            'createComment_form' => $form->createView(),
        ]);
    }
}
