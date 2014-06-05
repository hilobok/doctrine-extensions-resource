<?php

namespace Anh\DoctrineResource\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends EntityRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            if (null === $value) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->isNull($this->getPropertyName($property))
                );
            } elseif (is_array($value)) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->in($this->getPropertyName($property), $value)
                );
            } elseif ('' !== $value) {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->eq(
                            $this->getPropertyName($property),
                            sprintf(':%s', $property)
                        )
                    )
                    ->setParameter($property, $value)
                ;
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = null)
    {
        if (null === $sorting) {
            return;
        }

        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->orderBy($this->getPropertyName($property), $order);
            }
        }
    }
}
