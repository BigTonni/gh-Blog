<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPostsByCategoryId($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->where('c.id IN (:id)')
            ->setParameter(':id', $id)
            ->getQuery();
    }

  public function findPostsByAuthorId($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.author', 'u')
            ->where('u.id IN (:id)')
            ->setParameter(':id', $id)
            ->getQuery();
    }
}
