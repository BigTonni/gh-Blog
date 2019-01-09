<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * @var
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{_locale}", name="home", defaults={"_locale": "en"})
     */
    public function home(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $postsQuery = $em->getRepository(Post::class)->findLatest(Post::NUM_ITEMS);
        $posts = $paginator->paginate($postsQuery, $request->query->getInt('page', 1));

        if (!$postsQuery) {
            throw $this->createNotFoundException($this->translator->trans('exception.no_posts'));
        }

        return $this->render('home/content.twig', [
            'title' => $this->translator->trans('content.homepage_title'),
            'posts' => $posts,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rightSidebar(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('home/right_sidebar.html.twig', [
            'categories' => $categories,
        ]);
    }
}
