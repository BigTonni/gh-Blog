<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController.
 */
class SearchController extends AbstractController
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
     * @param PostRepository     $postRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     *
     * @Route("/{_locale}/post/search", defaults={"_locale": "en"}, methods={"GET"}, name="post_search")
     */
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $query = $postRepository->findBySearchQuery($request->query->get('q', ''),
            $request->query->get('l', 10));

        if (!$query) {
            throw $this->createNotFoundException($this->translator->trans('exception.search_query_not_result'));
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1), Post::NUM_ITEMS);

        return $this->render('post/search.html.twig', [
            'posts' => $posts,
            'title' => $this->translator->trans('search.search_title').' '.$request->query->get('q'),
        ]);
    }
}
