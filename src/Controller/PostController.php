<?php

namespace App\Controller;

use App\Form\PostType;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController.
 */
class PostController extends AbstractController
{
    /**
     * @param Post $post
     *
     * @return Response
     *
     * @Route("/post/show/{slug}", name="post_show")
     */
    public function show(Post $post): Response
    {
        $category = $post->getCategory();

        $em = $this->getDoctrine()->getManager();
        $countComment = $em->getRepository(Comment::class)->getCountCommentForPost($post->getId());

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'category' => $category,
            'countComment' => $countComment,
        ]);
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param Category           $category
     *
     * @return Response
     *
     * @Route("/category/{slug}", name="posts_in_category_show")
     */
    public function showPostsInCategory(Request $request, PaginatorInterface $paginator, Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByCategoryId($category->getId());

        if (!$query) {
            throw $this->createNotFoundException('There are no posts in this category');
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1));
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('home/content.twig', [
            'title' => 'Show Post in Category '.$category->getName(),
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param User               $user
     *
     * @return Response
     *
     * @Route("/author/{slug}", name="author_posts_show")
     */
    public function showAuthorPosts(Request $request, PaginatorInterface $paginator, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByAuthorId($user->getId());

        if (!$query) {
            throw $this->createNotFoundException('There are no posts in this author');
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1));
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('home/content.twig', [
            'title' => 'View author posts '.$user->getFullName(),
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     * @Route("/post/my", name="show_my_posts")
     */
    public function showMyPosts(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByAuthorId($this->getUser()->getId());

        if (!$query) {
            throw $this->createNotFoundException('You have no posts');
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1));
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('home/content.twig', [
            'title' => 'View author posts '.$this->getUser()->getFullName(),
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/post/new", name="post_new")
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $post = new Post($this->getUser());

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('post_new'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create New Post',
        ]);
    }

    /**
     * @param Request $request
     * @param Post    $post
     *
     * @return Response
     *
     * @Route("post/edit/{slug}", name="post_edit")
     */
    public function edit(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->checkUser($post)) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Post $post
     *
     * @return Response
     *
     * @Route("post/delete/{slug}", name="post_delete")
     */
    public function delete(Post $post): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->checkUser($post)) {
            return $this->redirectToRoute('home');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("post/search", methods={"GET"}, name="post_search")
     */
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $query = $postRepository->findBySearchQuery($request->query->get('q', ''),
            $request->query->get('l', 10));

        if (!$query) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('post/search.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("post/like/{slug}", name="post_like")
     */
    public function like(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $post->setLike($post->getLike() + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }

    private function checkUser(Post $post): bool
    {
        return $post->getAuthor() === $this->getUser();
    }
}
