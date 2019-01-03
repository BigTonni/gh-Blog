<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="home")
     */
    public function home(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $postsQuery = $em->getRepository(Post::class)->createQueryBuilder('p')->getQuery();
        $posts = $paginator->paginate($postsQuery, $request->query->getInt('page', 1));

        return $this->render('home/content.twig', [
            'title' => 'Show Posts',
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
