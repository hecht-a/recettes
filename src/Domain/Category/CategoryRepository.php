<?php

namespace App\Domain\Category;

use App\Infra\Repository\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Category[]
     */
    public function searchByName(string $q): array
    {
        return $this->createQueryBuilder('t')
            ->where('LOWER(t.name) LIKE :q')
            ->orderBy('LENGTH(t.name)', 'ASC')
            ->setMaxResults(3)
            ->setParameter('q', strtolower($q) . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string[] $names
     *
     * @return Category[]
     */
    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) IN (:name)')
            ->setParameter('name', array_map(fn (string $name) => strtolower($name), $names))
            ->getQuery()
            ->getResult();
    }
}
