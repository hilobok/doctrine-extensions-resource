<?php

namespace Anh\DoctrineResource;

trait ResourceRepositoryTrait
{
    protected $paginator;

    protected $ruleResolver;

    protected $adapter;

    protected $resourceAlias = 'resource';

    protected $resourceName;

    /**
     * {@inheritdoc}
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function setRuleResolver(RuleResolver $ruleResolver)
    {
        $this->ruleResolver = $ruleResolver;

        return $this;
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

    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    public function setResourceAlias($resourceAlias)
    {
        $this->resourceAlias = $resourceAlias;

        return $this;
    }

    protected function prepareQueryBuilder($queryBuilder, array $criteria = null, array $sorting = null, $limit = null, $offset = null)
    {
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
        return $this->createQueryBuilder($this->resourceAlias);
    }

    protected function getAdapter()
    {
        if ($this->adapter === null) {
            $adapterClass = $this->getAdapterClass();
            $this->adapter = new $adapterClass(
                $this->resourceName,
                $this->resourceAlias,
                $this->ruleResolver
            );
        }

        return $this->adapter;
    }

    abstract protected function getAdapterClass();
}
