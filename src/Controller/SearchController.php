<?php

namespace App\Controller;

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
     * @param $maxItemPerPage
     *
     * @return Response
     *
     * @Route("/{_locale}/post/search/{maxItemPerPage}/", defaults={"_locale": "en", "maxItemPerPage" : "10"},
     *        requirements={"maxItemPerPage"="\d+"}, methods={"GET"}, name="post_search")
     */
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator, $maxItemPerPage): Response
    {
        $query = $postRepository->findBySearchQuery($request->query->get('q', ''),
            $request->query->get('l', 10));

        if (!$query) {
            throw $this->createNotFoundException($this->translator->trans('exception.search_query_not_result'));
        }

        $posts = $paginator->paginate($query, $request->query->getInt('page', 1), $maxItemPerPage);

        return $this->render('home/content.twig', [
            'posts' => $posts,
            'title' => $this->translator->trans('search.search_title').' '.$request->query->get('q'),
        ]);
    }
}
