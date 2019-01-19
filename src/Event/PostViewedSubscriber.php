<?php

namespace App\Event;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PostViewedSubscriber.
 */
class PostViewedSubscriber extends AbstractController implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PostViewedEvent::NAME => 'onPostViewed',
        ];
    }

    /**
     * @param PostViewedEvent $postViewedEvent
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onPostViewed(PostViewedEvent $postViewedEvent)
    {
        $post = $postViewedEvent->getPost();
        $em = $this->getDoctrine()->getManager();

        $postInNotification = $em->getRepository(Notification::class)->findBy(
            [
                'post' => $postViewedEvent->getPost(),
                'user' => $this->getUser(),
            ]
        );

        if ($postInNotification) {
            $em->getRepository(Notification::class)->updateReadStatus($post, $this->getUser(), true);
        }
    }
}
