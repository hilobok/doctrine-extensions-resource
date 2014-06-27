<?php

namespace Anh\DoctrineResource;

trait ResourceRepositoryTrait
{
    protected $paginator;

    protected $adapter;

    protected $alias = 'resource';

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
        $queryBuilder = $this->prepareQueryBuilder(
            $this->getQueryBuilder(),
            $criteria,
            $sorting
        );

        return $this->paginator->paginate($queryBuilder, $page, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(array $criteria = null, array $sorting = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->prepareQueryBuilder(
            $this->getQueryBuilder(),
            $criteria,
            $sorting,
            $limit,
            $offset
        );

        return $this->getAdapter()->getResult($queryBuilder);
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    protected function prepareQueryBuilder($queryBuilder, array $criteria = null, array $sorting = null, $limit = null, $offset = null)
    {
        $this->getAdapter()
            ->setAlias($this->getAlias())
            ->applyCriteria($queryBuilder, $criteria)
            ->applySorting($queryBuilder, $sorting)
            ->applyLimit($queryBuilder, $limit)
            ->applyOffset($queryBuilder, $offset)
        ;

        return $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
    }

    protected function getAdapter()
    {
        if ($this->adapter === null) {
            $adapterClass = $this->getAdapterClass();
            $this->adapter = new $adapterClass;
        }

        return $this->adapter;
    }

    abstract protected function getAdapterClass();
}
