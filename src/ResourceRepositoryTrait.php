<?php

namespace Anh\DoctrineResource;

trait ResourceRepositoryTrait
{
    /**
     * {@inheritdoc}
     */
    public function setPaginator(/* ResourcePaginatorInterface */ $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($page, $limit, array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->prepareQueryBuilder($criteria, $sorting);

        return $this->paginator->paginate($queryBuilder, $page, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(array $criteria = null, array $sorting = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->prepareQueryBuilder($criteria, $sorting);

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    protected function prepareQueryBuilder(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return sprintf('%s.%s', $this->getAlias(), $name);
        }

        return $name;
    }

    protected function getAlias()
    {
        return 'resource';
    }
}