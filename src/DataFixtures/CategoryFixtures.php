<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class CategoryFixtures.
 */
class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadCategory($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCategory(ObjectManager $manager)
    {
        foreach ($this->getCategory() as $index => $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $this->addReference('category-'.$name, $category);
        }
        $manager->flush();
    }

    /**
     * @return array
     */
    public function getCategory(): array
    {
        return [
            'Test category 1',
            'Test category 2',
            'CMS',
            'IT news',
            'Analytics',
            'Internet Marketing',
            'Cases',
            'Contextual advertising',
            'Website promotion',
            'Website Development',
        ];
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 3;
    }
}
