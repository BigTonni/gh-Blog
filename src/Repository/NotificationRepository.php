<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    /**
     * NotificationRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param Post $post
     * @param User $user
     * @param bool $status
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateReadStatus(Post $post, User $user, bool $status)
    {
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.isRead', ':status')
            ->where('u.post = :post')
            ->andWhere('u.user = :user')
            ->setParameter(':status', $status)
            ->setParameter(':post', $post)
            ->setParameter(':user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
