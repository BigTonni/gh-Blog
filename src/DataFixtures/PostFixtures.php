<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class PostFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadPosts($manager);
    }

    public function loadPosts(ObjectManager $manager)
    {
        $post = new Post($this->getReference('admin-user'));
        $post->setTitle('First post title');
        $post->setContent('This is a little framework that makes ease-of-use its trademark.
             It is an open-source framework, licensed under the GNU license.
             It contains several features that can be implemented with only a couple of lines of code.
             TwistPHP was born as a private project, and as improvements took place, its source-code became public and 
             moved to a GitHub repository in July 2014. The first official release occurred with TwistPHP 2.3.4 in 
             November 2014. Throughout the years, this software has had plenty of time to improve and now, with its last
             stable release (3.0.5), it has a complete MVC architecture, object-oriented design, and brand new method 
             for connecting to the database and creating MySQL queries. The way it has built facilitates its 
             extendibility and reliability. If you want to give it a try or help the project grow you can support it 
             through its GitHub repository below.');
        $post->setCategory($this->getReference('category-Test category 1'));

        foreach (range(1, 3) as $i) {
            $comment = new Comment();
            $comment->setContent($this->getRandomComment(random_int(255, 512)));
            $post->addComment($comment);
            $comment->setAuthor($this->getReference('user-user'));
        }
        $manager->persist($post);

        $post1 = new Post($this->getReference('user-user'));
        $post1->setTitle('Second post title');
        $post1->setContent('Yii is a PHP framework that has been developed by the same creators of Prado. To be honest, it was born as 
             an attempt to fix all the problems with Prado (see Prado review). Like other frameworks, it is released under 
             the new BSD License, thus, it is possible to use it and create open-source web applications for free.
             The first beta version was released in 2006 after several months of development, followed by its official 
             1.0 version in December 2008. A more complete version was released in January 2010; it included a form builder, 
             ActiveRecord, an internal unit test library, and several more features that make Yii a more complete PHP 
             framework than its predecessor. From the beginning, Yii developers decided to keep this project up-to-date
             with the most recent technology. The current version 2.0.15 was released in March 2018 and fully supports 
             PHP 7. There aren’t particular features or special peculiarities to Yii, but it is a pretty solid framework
             and among the characteristics of the latest release you will haveu want to give it a try or help the project grow you can support it 
             through its GitHub repository below.');
        $post1->setCategory($this->getReference('category-Test category 2'));

        foreach (range(1, 3) as $i) {
            $comment = new Comment();
            $comment->setContent($this->getRandomComment(random_int(255, 512)));
            $post1->addComment($comment);
            $comment->setAuthor($this->getReference('user-user'));
        }
        $manager->persist($post1);

        $manager->flush();
    }

    public function getRandomComment(int $maxLength = 255): string
    {
        $phrases = $this->getComment();
        shuffle($phrases);
        while (mb_strlen($text = implode('. ', $phrases).'.') > $maxLength) {
            array_pop($phrases);
        }

        return $text;
    }

    public function getComment(): array
    {
        return [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'Convallis posuere morbi leo urna molestie. Netus et malesuada fames ac turpis.',
            'Consectetur purus ut faucibus pulvinar elementum integer.',
            'Ut eu sem integer vitae justo eget magna fermentum.',
            'Vitae auctor eu augue ut.',
            'Fermentum dui faucibus in ornare quam viverra orci sagittis eu.',
            'Sit amet consectetur adipiscing elit pellentesque habitant morbi.',
            'Non quam lacus suspendisse faucibus interdum posuere lorem.',
            'Maecenas pharetra convallis posuere morbi leo.',
        ];
    }

  public function getOrder()
    {
        return 3;
    }
}
