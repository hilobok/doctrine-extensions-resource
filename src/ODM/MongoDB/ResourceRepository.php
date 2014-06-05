<?php

namespace Anh\DoctrineResource\ODM\MongoDB;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends DocumentRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    /**
     * {@inheritdoc}
     */
    public function fetch(array $criteria = null, array $sorting = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->prepareQueryBuilder($criteria, $sorting);

        if (null !== $limit) {
            $queryBuilder->limit($limit);
        }

        if (null !== $offset) {
            $queryBuilder->skip($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->execute()
        ;
    }

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
            $queryBuilder
                ->field($property)->equals($value)
            ;
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
            $queryBuilder->sort($property, $order);
        }
    }
}
