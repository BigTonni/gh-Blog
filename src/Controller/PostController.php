<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\PostFilterType;
use App\Form\PostType;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     *
     * @Route("/post/all/", name="posts_all_show")
     */
    public function showAll(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $postsQuery = $em->getRepository(Post::class)->createQueryBuilder('p')->getQuery();
        $posts = $paginator->paginate($postsQuery, $request->query->getInt('page', 1), 10);

        return $this->render('home/content.twig', [
            'title' => 'Show Posts',
            'posts' => $posts,
        ]);
    }

    /**
     * @param Post $post
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/post/show/{slug}", name="post_show")
     */
    public function show(Post $post): Response
    {
        $em = $this->getDoctrine()->getManager();
        $countComment = $em->getRepository(Comment::class)->getCountCommentForPost($post->getId());

        return $this->render('post/show.html.twig', [
            'post' => $post,
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

        return $this->render('home/content.twig', [
            'title' => 'Show Post in Category '.$category->getName(),
            'posts' => $posts,
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

        return $this->render('home/content.twig', [
            'title' => 'View author posts '.$user->getFullName(),
            'posts' => $posts,
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

        return $this->render('home/content.twig', [
            'title' => 'View author posts '.$this->getUser()->getFullName(),
            'posts' => $posts,
        ]);
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param Tag                $tag
     *
     * @return Response
     *
     * @Route("/tag/{slug}", name="posts_with_tag_show")
     */
    public function showPostsWithTag(Request $request, PaginatorInterface $paginator, Tag $tag): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByTagId($tag->getId());

        if (!$query) {
            throw $this->createNotFoundException('There are no posts with this tag.');
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('home/content.twig', [
            'title' => 'View Posts Tagged With - '.$tag->getName(),
            'posts' => $posts,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/post/new", name="post_new")
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $post->setAuthor($this->getUser());

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
     * @IsGranted("ROLE_ADMIN")
     * @Route("post/edit/{slug}", name="post_edit")
     */
    public function edit(Request $request, Post $post): Response
    {
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
     * @IsGranted("ROLE_ADMIN")
     * @Route("post/delete/{slug}", name="post_delete")
     */
    public function delete(Post $post): Response
    {
        if (!$this->checkUser($post)) {
            return $this->redirectToRoute('home');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @param Request            $request
     * @param PostRepository     $postRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     *
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
     * @param Post $post
     *
     * @return Response
     *
     * @IsGranted("ROLE_USER")
     * @Route("post/like/{slug}", name="post_like")
     */
    public function like(Post $post): Response
    {
        $post->setLike($post->getLike() + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }

    /**
     * @param Post $post
     *
     * @return bool
     */
    private function checkUser(Post $post): bool
    {
        return $post->getAuthor() === $this->getUser();
    }
}
