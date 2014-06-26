<?php

namespace Anh\DoctrineResource;

trait ResourceRepositoryTrait
{
    protected $paginator;

    protected $adapter;

    /**
     * {@inheritdoc}
     */
    public function setPaginator($paginator)
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
        $adapter = $this->getAdapter();

        if (null !== $limit) {
            $adapter->buildLimit($queryBuilder, $limit);
        }

        if (null !== $offset) {
            $adapter->buildOffset($queryBuilder, $offset);
        }

        return $adapter->getResult($queryBuilder);
    }

    protected function prepareQueryBuilder(array $criteria = null, array $sorting = null, QueryBuilder $queryBuilder = null)
    {
        $queryBuilder = $queryBuilder ?: $this->getQueryBuilder();

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

    protected function getAlias()
    {
        return 'resource';
    }

    protected function getAdapter()
    {
        if ($this->adapter === null) {
            $adapterClass = $this->getAdapterClass();
            $this->adapter = new $adapterClass;
        }

        return $this->adapter;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     */
    protected function applyCriteria($queryBuilder, array $criteria = null)
    {
        if (empty($criteria)) {
            return;
        }

        $this->getAdapter()
            ->setAlias($this->getAlias())
            ->buildCriteria($queryBuilder, $criteria)
        ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     */
    protected function applySorting($queryBuilder, array $sorting = null)
    {
        if (empty($sorting)) {
            return;
        }

        $this->getAdapter()
            ->setAlias($this->getAlias())
            ->buildSorting($queryBuilder, $sorting)
        ;
    }
}
