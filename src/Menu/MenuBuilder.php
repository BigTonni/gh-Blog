<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuBuilder
{
    use ContainerAwareTrait;

    private $factory;
    private $checker;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->checker = $authorizationChecker;
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

        if (!$this->checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild('Login', ['route' => 'app_login'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
            $menu->addChild('Register', ['route' => 'app_register'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
        } else {
            $menu->addChild('Logout', ['route' => 'app_logout'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
        }

        return $menu;
    }
}
