<?php

namespace App\Event;

use App\Entity\Notification;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostPublishedSubscriber extends AbstractController implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PostPublishedEvent::NAME => 'onPostPublished',
        ];
    }

    public function onPostPublished(PostPublishedEvent $postPublishedEvent)
    {
        $post = $postPublishedEvent->getPost();
        $category = $post->getCategory();

        $em = $this->getDoctrine()->getManager();

        $subscribers = $em->getRepository(Subscription::class)->findBy(
            [
                'category' => $category,
            ]
        );

        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $notification = new Notification();
                $notification->setUser($subscriber->getUser());
                $notification->setPost($post);

                $em->persist($notification);
            }

            $em->flush();
        }
    }
}
