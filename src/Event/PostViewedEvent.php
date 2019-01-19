<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostViewedEvent.
 */
class PostViewedEvent extends Event
{
    public const NAME = 'post.viewed';

    /**
     * @var Post
     */
    private $post;

    /**
     * PostViewedEvent constructor.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }
}
