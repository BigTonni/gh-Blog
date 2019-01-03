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
        $menu->addChild('Home', ['route' => 'home'])
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
            if ($this->checker->isGranted('ROLE_ADMIN')) {
                $menu->addChild('Create Post', ['route' => 'post_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
            }
            $menu->addChild('My Posts', ['route' => 'show_my_posts'])
                ->setAttributes([
                    'class' => 'nav-item', ]
                );
            $menu->addChild('Logout', ['route' => 'app_logout'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
        }

        return $menu;
    }
}
