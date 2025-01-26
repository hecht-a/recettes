<?php

namespace App\Domain\Allergen;

use App\Infra\Repository\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<Allergen>
 *
 * @method Allergen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Allergen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Allergen[]    findAll()
 * @method Allergen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllergenRepository extends AbstractEntityRepository
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
     * @return Allergen[]
     */
    public function searchByName(string $q): array
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.name) LIKE :q')
            ->orderBy('LENGTH(a.name)', 'ASC')
            ->setMaxResults(3)
            ->setParameter('q', strtolower($q) . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string[] $names
     *
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

    /**
     * @return array<string, AllergenDto[]>
     */
    public function findAllergensByLetters(): array
    {
        $dto = AllergenDto::class;
        $words = $this->createQueryBuilder('a')
            ->distinct()
            ->select("NEW {$dto}(a.id, a.name, a.description, a.slug)")
            ->orderBy('a.name')
            ->getQuery()
            ->getResult();

        return collect($words)
            ->groupBy(fn (AllergenDto $item) => strtolower($item->name[0]))
            ->toArray();
    }
}
