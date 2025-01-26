<?php

namespace App\Infra\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of object
 *
 * @template-extends ServiceEntityRepository<T>
 */
abstract class AbstractEntityRepository extends ServiceEntityRepository
{
    /**
     * @phpstan-return T
     */
    public function findOneOrCreate(array $criteria, array $data, bool $flush = false): object
    {
        $entity = $this->findOneBy($criteria);

        if (null === $entity) {
            $entity = new (self::getClassName())();
            foreach ($data as $field => $value) {
                $setter = 'set' . ucfirst($field);
                $entity->$setter($value);
            }

            $this->_em->persist($entity);
            if ($flush) {
                $this->_em->flush();
            }
        }

        return $entity;
    }
}
