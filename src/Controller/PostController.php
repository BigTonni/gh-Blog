<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Tag;
use App\Form\PostType;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PostController.
 */
class PostController extends AbstractController
{
    /**
     * @var
     */
    private $translator;

    /**
     * PostController constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     *
     * @Route("/{_locale}/post/all", defaults={"_locale": "en"}, name="posts_all_show")
     */
    public function showAll(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $postsQuery = $em->getRepository(Post::class)->createQueryBuilder('p')->getQuery();
        $posts = $paginator->paginate($postsQuery, $request->query->getInt('page', 1), Post::NUM_ITEMS);

        if (!$postsQuery) {
            throw $this->createNotFoundException($this->translator->trans('exception.no_posts'));
        }

        return $this->render('home/content.twig', [
            'title' => $this->translator->trans('post.all_posts_title'),
            'posts' => $posts,
        ]);
    }

    /**
     * @param Post        $post
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/{_locale}/post/show/{slug}", defaults={"_locale": "en"}, name="post_show")
     */
    public function show(Post $post, Breadcrumbs $breadcrumbs): Response
    {
        $em = $this->getDoctrine()->getManager();
        $countComment = $em->getRepository(Comment::class)->getCountCommentForPost($post->getId());
        $countLike = $em->getRepository(Like::class)->getCountLikeForPost($post->getId());

        $breadcrumbs->prependRouteItem('menu.home', 'home');
        $breadcrumbs->addRouteItem($post->getCategory()->getName(), 'posts_in_category_show', [
            'slug' => $post->getCategory()->getSlug(),
        ]);
        $breadcrumbs->addRouteItem($post->getTitle(), 'post_show', [
            'slug' => $post->getSlug(),
        ]);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'countComment' => $countComment,
            'countLike' => $countLike,
            'title' => $post->getTitle(),
        ]);
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param Category           $category
     *
     * @return Response
     *
     * @Route("/{_locale}/category/{slug}", defaults={"_locale": "en"}, name="posts_in_category_show")
     */
    public function showPostsInCategory(Request $request, PaginatorInterface $paginator, Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByCategoryId($category->getId());

        if (!$query) {
            throw $this->createNotFoundException($this->translator->trans('exception.no_posts_in_category'));
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1), Post::NUM_ITEMS);

        return $this->render('home/content.twig', [
            'title' => $this->translator->trans('post.posts_in_category_title').' '.$category->getName(),
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
     * @Route("/{_locale}/author/{slug}", defaults={"_locale": "en"}, name="author_posts_show")
     */
    public function showAuthorPosts(Request $request, PaginatorInterface $paginator, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByAuthorId($user->getId());

        if (!$query) {
            throw $this->createNotFoundException($this->translator->trans('exception.author_no_posts'));
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1), Post::NUM_ITEMS);

        return $this->render('home/content.twig', [
            'title' => $this->translator->trans('post.author_posts_title').' '.$user->getFullName(),
            'posts' => $posts,
        ]);
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     *
     * @Route("/{_locale}/post/my", defaults={"_locale": "en"}, name="show_my_posts")
     */
    public function showMyPosts(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByAuthorId($this->getUser()->getId());

        if (!$query) {
            throw $this->createNotFoundException($this->translator->trans('exception.you_no_posts'));
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1), Post::NUM_ITEMS);

        return $this->render('home/content.twig', [
            'title' => $this->translator->trans('post.author_posts_title').' '.$this->getUser()->getFullName(),
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
     * @Route("/{_locale}/tag/{slug}", defaults={"_locale": "en"}, name="posts_with_tag_show")
     */
    public function showPostsWithTag(Request $request, PaginatorInterface $paginator, Tag $tag): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findPostsByTagId($tag->getId());

        if (!$query) {
            throw $this->createNotFoundException($this->translator->trans('exception.no_posts_with_tag'));
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1), Post::NUM_ITEMS);

        return $this->render('home/content.twig', [
            'title' => $this->translator->trans('post.posts_with_tag_title').' '.$tag->getName(),
            'posts' => $posts,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{_locale}/post/new", defaults={"_locale": "en"}, name="post_new")
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
            'title' => $this->translator->trans('post.create_title'),
        ]);
    }

    /**
     * @param Request $request
     * @param Post    $post
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{_locale}/post/edit/{slug}", defaults={"_locale": "en"}, name="post_edit")
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
            'title' => $this->translator->trans('post.edit_title'),
        ]);
    }

    /**
     * @param Post $post
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{_locale}/post/delete/{slug}", defaults={"_locale": "en"}, name="post_delete")
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
     * @param Post $post
     *
     * @return bool
     */
    private function checkUser(Post $post): bool
    {
        return $post->getAuthor() === $this->getUser();
    }
}
