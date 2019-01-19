<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Notification;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends AbstractController
{
    /**
     * @param Category $category
     *
     * @return Response
     *
     * @IsGranted("ROLE_USER")
     * @Route("/{_locale}/category/subscribe/{slug}", defaults={"_locale": "en"}, name="category_subscribe")
     */
    public function subscribe(Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $existSubscription = $em->getRepository(Subscription::class)->findBy(
            [
                'user' => $this->getUser(),
                'category' => $category,
            ]
        );

        if (!$existSubscription) {
            $subscribers = new Subscription();
            $subscribers->setUser($this->getUser());
            $subscribers->setCategory($category);

            $em->persist($subscribers);
        }

        $em->flush();

        return $this->redirectToRoute('posts_in_category_show', ['slug' => $category->getSlug()]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notification(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $notification = $em->getRepository(Notification::class)->findBy([
            'user' => $this->getUser(),
            'isRead' => false,
        ]);

        return $this->render('notification/notification.html.twig', [
            'notification' => $notification,
        ]);
    }
}
