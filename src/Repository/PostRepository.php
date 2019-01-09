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
    /**
     * PostRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param $limit
     *
     * @return mixed
     */
    public function findLatest($limit)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findPostsByCategoryId($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->where('c.id IN (:id)')
            ->setParameter(':id', $id)
            ->orderBy('c.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findPostsByAuthorId($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.author', 'u')
            ->where('u.id IN (:id)')
            ->setParameter(':id', $id)
            ->orderBy('u.email', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findPostsByTagId($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.tags', 't')
            ->where('t.id IN (:id)')
            ->setParameter(':id', $id)
            ->orderBy('t.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $rawQuery
     * @param int    $limit
     *
     * @return array
     */
    public function findBySearchQuery(string $rawQuery, int $limit = Post::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);
        if (0 === \count($searchTerms)) {
            return [];
        }
        $queryBuilder = $this->createQueryBuilder('p');
        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('p.title LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $queryBuilder
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $query
     *
     * @return string
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * @param string $searchQuery
     *
     * @return array
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
