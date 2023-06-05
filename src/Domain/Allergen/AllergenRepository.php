<?php

namespace App\Domain\Allergen;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Allergen>
 *
 * @method Allergen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Allergen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Allergen[]    findAll()
 * @method Allergen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllergenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Allergen::class);
    }

    public function save(Allergen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Allergen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $q
     * @return Allergen[]
     */
    public function searchByName(string $q): array
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.name) LIKE :q')
            ->orderBy('LENGTH(a.name)', 'ASC')
            ->setMaxResults(3)
            ->setParameter('q', strtolower($q).'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string[] $names
     * @return Allergen[]
     */
    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.name) IN (:name)')
            ->setParameter('name', array_map(fn (string $name) => strtolower($name), $names))
            ->getQuery()
            ->getResult();
    }
}
