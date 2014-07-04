<?php

namespace Anh\DoctrineResource;

trait ResourceRepositoryTrait
{
    protected $paginator;

    protected $rules;

    protected $adapter;

    protected $alias = 'r';

    /**
     * {@inheritdoc}
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function setRules(array $rules = null)
    {
        $this->rules = $rules;

        return $this;
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
        $queryBuilder = $this->prepareQueryBuilder($criteria, $sorting, $limit, $offset);

        return $this->getAdapter()->getResult($queryBuilder);
    }

    public function prepareQueryBuilder(array $criteria = null, array $sorting = null, $limit = null, $offset = null, $queryBuilder = null)
    {
        $queryBuilder = $queryBuilder ?: $this->getQueryBuilder();

        $this->getAdapter()
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
        return $this->createQueryBuilder($this->alias);
    }

    protected function getAdapter()
    {
        if ($this->adapter === null) {
            $adapterClass = $this->getAdapterClass();
            $this->adapter = new $adapterClass(
                $this->alias,
                $this->rules
            );
        }

        return $this->adapter;
    }

    abstract protected function getAdapterClass();
}
