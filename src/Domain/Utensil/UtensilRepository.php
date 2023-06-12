<?php

namespace App\Domain\Utensil;

use App\Domain\Allergen\AllergenDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utensil>
 *
 * @method Utensil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utensil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utensil[]    findAll()
 * @method Utensil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtensilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utensil::class);
    }

    public function save(Utensil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Utensil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Utensil[]
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
     *
     * @return Utensil[]
     */
    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.name) IN (:name)')
            ->setParameter('name', array_map(fn (string $name) => strtolower($name), $names))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<string, Utensil[]>
     */
    public function findUtensilsByLetters(): array
    {
        $words = $this->createQueryBuilder('u')
            ->orderBy('u.name')
            ->getQuery()
            ->getResult();

        return collect($words)
            ->sortBy(fn (Utensil $item) => strtolower($item->getName()))
            ->groupBy(fn (Utensil $item) => strtolower($item->getName()[0]))
            ->toArray();
    }
}
