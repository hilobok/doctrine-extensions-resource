<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends DocumentRepository implements ResourceRepositoryInterface
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
            if (!empty($value)) {
                $queryBuilder
                    ->andWhere(
                        sprintf('%s = :%s', $this->getPropertyName($property), $property)
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
                $queryBuilder
                    ->orderBy()
                    ->{$order}()
                    ->field(
                        $this->getPropertyName($property)
                    )
                ;
            }
        }

        $queryBuilder->end();
    }
}
