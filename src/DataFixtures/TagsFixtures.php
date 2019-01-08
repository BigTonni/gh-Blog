<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class TagsFixtures.
 */
class TagsFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadTags($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadTags(ObjectManager $manager)
    {
        foreach ($this->getTags() as $index => $name) {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
            $this->addReference('tag-'.$name, $tag);
        }
        $manager->flush();
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return [
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 4;
    }
}
