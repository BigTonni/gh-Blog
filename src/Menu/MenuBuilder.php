<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuBuilder.
 */
class MenuBuilder
{
    use ContainerAwareTrait;

    /**
     * @var FactoryInterface
     */
    private $factory;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $checker;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface              $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->checker = $authorizationChecker;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(): \Knp\Menu\ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav');
        $menu->addChild('menu.home', ['route' => 'home'])
            ->setAttributes([
                    'class' => 'nav-item', ]
            );
        $menu->addChild('menu.all_posts', ['route' => 'posts_all_show'])
            ->setAttributes([
                    'class' => 'nav-item', ]
            );

        if (!$this->checker->isGranted('ROLE_USER')) {
            $menu->addChild('menu.login', ['route' => 'app_login'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
            $menu->addChild('menu.register', ['route' => 'app_register'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
        } else {
            if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                $menu->addChild('menu.admin_panel', ['route' => 'sonata_admin_dashboard'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
            }
            if ($this->checker->isGranted('ROLE_ADMIN')) {
                $menu->addChild('menu.create_post', ['route' => 'post_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
                $menu->addChild('menu.my_posts', ['route' => 'show_my_posts'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
            }
            $menu->addChild('menu.logout', ['route' => 'app_logout'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
        }

        return $menu;
    }
}
