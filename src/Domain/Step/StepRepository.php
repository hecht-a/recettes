<?php

namespace App\Domain\Step;

use App\Infra\Repository\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<Step>
 *
 * @method Step|null find($id, $lockMode = null, $lockVersion = null)
 * @method Step|null findOneBy(array $criteria, array $orderBy = null)
 * @method Step[]    findAll()
 * @method Step[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StepRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Step::class);
    }

    public function save(Step $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Step $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
