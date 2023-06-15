<?php

namespace App\Domain\Recipe;

use App\Domain\Auth\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
        ->orderBy('r.id');

        return $qb->select('r');
    }

    public function findFavoritesQuery(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.usersFavorite', 'user')
            ->where('user = :user')
            ->setParameter('user', $user);

        return $qb->select('r');
    }

    /**
     * @return Recipe[]
     */
    public function searchByName(string $q): array
    {
        return $this->createQueryBuilder('r')
            ->where('LOWER(r.name) LIKE :q')
            ->orderBy('LENGTH(r.name)', 'ASC')
            ->setMaxResults(3)
            ->setParameter('q', strtolower($q).'%')
            ->getQuery()
            ->getResult();
    }
}
