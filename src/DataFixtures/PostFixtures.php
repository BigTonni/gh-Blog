<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadPosts($manager);
    }

    private function loadPosts(ObjectManager $manager)
    {
        foreach ($this->getPostData() as [$title, $content, $category]) {
            $post = new Post();
            $post->setTitle($title);
            $post->setContent($content);
            $post->addCategory(...$category);

            foreach (range(1, 3) as $i) {
                $comment = new Comment();
                $comment->setContent($this->getRandomComment(random_int(255, 512)));
                $post->addComment($comment);
            }
            $manager->persist($post);
        }
        $manager->flush();
    }

    private function getPostData()
    {
        $posts = [];
        foreach ($this->getPostTtile() as $i => $title) {
            $posts[] = [
                $title,
                $this->getPostContent(),
                $this->getRandomCategory(),
            ];
        }

        return $posts;
    }

    public function getPostTtile()
    {
        return [
            '10 Tips That Will Make You Influential In PHP SYMFONY',
            'How To Teach PHP SYMFONY Better Than Anyone Else',
            'Take 10 Minutes to Get Started With PHP SYMFONY',
            'Want A Thriving Business? Focus On PHP SYMFONY!',
            'You Don\'t Have To Be A Big Corporation To Start PHP SYMFONY',
            'PHP SYMFONY: What A Mistake!',
            'Old School PHP SYMFONY',
            'Secrets To PHP SYMFONY – Even In This Down Economy',
            'How To Win Buyers And Influence Sales with PHP SYMFONY',
            '10 Secret Things You Didn\'t Know About PHP SYMFONY',
            'The Next 3 Things To Immediately Do About PHP SYMFONY',
            'Succeed With PHP SYMFONY In 24 Hours',
            '5 Things To Do Immediately About PHP SYMFONY',
            'What You Can Learn From Bill Gates About PHP SYMFONY',
            'Do PHP SYMFONY Better Than Barack Obama',
            'How To Use PHP SYMFONY To Desire',
            'How To Become Better With PHP SYMFONY In 10 Minutes',
            'A Guide To PHP SYMFONY At Any Age',
            'PHP SYMFONY Is Bound To Make An Impact In Your Business',
            '9 Ways PHP SYMFONY Can Make You Invincible]',
        ];
    }

    public function getPostContent(): string
    {
        $postContent = [
            'This is a little framework that makes ease-of-use its trademark.
             It is an open-source framework, licensed under the GNU license.
             It contains several features that can be implemented with only a couple of lines of code.
             TwistPHP was born as a private project, and as improvements took place, its source-code became public and 
             moved to a GitHub repository in July 2014. The first official release occurred with TwistPHP 2.3.4 in 
             November 2014. Throughout the years, this software has had plenty of time to improve and now, with its last
             stable release (3.0.5), it has a complete MVC architecture, object-oriented design, and brand new method 
             for connecting to the database and creating MySQL queries. The way it has built facilitates its 
             extendibility and reliability. If you want to give it a try or help the project grow you can support it 
             through its GitHub repository below.',
            'o effectively describe this framework, the story needs to start with TYPO3.It is a free and open-source 
             CMS developed more than 20 years ago (initial release dated 1998).Much used in German-speaking countries 
             but is also available in more than 50 languages, TYPO3 is used to build any type of website.Just to give an 
             idea of how big this CMS is, its code has been edited and improved from more than 300 contributors, and, at 
             the moment, it has been installed more than 500,000 times.TYPO3 Flow was a branch of TYPO3, The team wanted 
             to create a product that would be modern and could have been used independently from TYPO3. After several
             months of development, the beta was released in August 2011 — it also is an open-source product.
             The latest release is the 4.2.4 and it is dated October 18, 2017.The code is the base code of TYPO3 Neos 
             but, as stated, it can be used even without the CMS. It has been written following all the latest principles 
             of coding such us the MVC paradigm, AOP (Aspect-Oriented-Programming), DDD (Domain-Driven-Design), and TDD
             (Test-Driven-Development), etc. For this reason, the software requires version 5.3 or newer. As for 
             databases, it uses Doctrine 2 and can be interfaced with MySQL and PostgreSQL. Another interesting feature 
             about TYPO3 Flow is Fluid; Fluid is its template engine. It supports all logical structures of a programming 
             language such as condition,s iterations, loops, etc, by providing a really easy syntax and avoiding the use 
             of PHP in the template files.',
            'Yii is a PHP framework that has been developed by the same creators of Prado. To be honest, it was born as 
             an attempt to fix all the problems with Prado (see Prado review). Like other frameworks, it is released under 
             the new BSD License, thus, it is possible to use it and create open-source web applications for free.
             The first beta version was released in 2006 after several months of development, followed by its official 
             1.0 version in December 2008. A more complete version was released in January 2010; it included a form builder, 
             ActiveRecord, an internal unit test library, and several more features that make Yii a more complete PHP 
             framework than its predecessor. From the beginning, Yii developers decided to keep this project up-to-date
             with the most recent technology. The current version 2.0.15 was released in March 2018 and fully supports 
             PHP 7. There aren’t particular features or special peculiarities to Yii, but it is a pretty solid framework
             and among the characteristics of the latest release you will have',
        ];

        $randKey = array_rand($postContent);

        return $postContent[$randKey];
    }

    private function getRandomCategory(): array
    {
        $category = new CategoryFixtures();
        $categoryNames = $category->getCategory();
        shuffle($categoryNames);
        $chosenCategory = \array_slice($categoryNames, 0, random_int(2, 4));

        return array_map(function ($categoryName) {
            return $this->getReference('category-'.$categoryName);
        }, $chosenCategory);
    }

    private function getRandomComment(int $maxLength = 255): string
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
}
