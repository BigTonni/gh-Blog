<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Component\EventDispatcher\Event;

class PostPublishedEvent extends Event
{
    public const NAME = 'post.published';

    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}
