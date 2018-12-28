<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav');
        $menu->addChild('Home', ['route' => 'home'])
            ->setAttributes([
            'class' => 'nav-item', ]
            );
        $menu->addChild('Create Post', ['route' => 'post_create'])
            ->setAttributes([
                    'class' => 'nav-item', ]
            );

        return $menu;
    }
}
