<?php

namespace Anh\DoctrineResource\ODM\MongoDB;

use Anh\DoctrineResource\AbstractQueryBuilderAdapter;

class QueryBuilderAdapter extends AbstractQueryBuilderAdapter
{
    protected $typeMap = array(
        'and' => 'addAnd',
        'or' => 'addOr',
    );

    protected $operatorMap = array(
        '=' => 'equals',
        '==' => 'equals',
        '<>' => 'notEqual',
        '!=' => 'notEqual',
        '>' => 'gt',
        '>=' => 'gte',
        '<' => 'lt',
        '<=' => 'lte',
        'in' => 'in',
        'not in' => 'notIn',
        'size' => 'size',
        'exists' => 'exists',
        'type' => 'type',
        'all' => 'all',
        'where' => 'where',
        'map' => 'map',
        'reduce' => 'reduce',
    );

    public function applyCriteria($queryBuilder, array $criteria = null)
    {
        if (empty($criteria)) {
            return $this;
        }

        $this->parameters = array();
        $this->processCriteria($queryBuilder, '#and', $criteria);

        return $this;
    }

    protected function createOrderBy($builder, $field, $order)
    {
        return $builder->sort($field, $order);
    }

    public function applyLimit($queryBuilder, $limit = null)
    {
        if (empty($limit)) {
            return $this;
        }

        $queryBuilder->limit($limit);

        return $this;
    }

    public function applyOffset($queryBuilder, $offset = null)
    {
        if ($offset === null) {
            return $this;
        }

        $queryBuilder->skip($offset);

        return $this;
    }

    public function getResult($queryBuilder)
    {
        return $queryBuilder->getQuery()->execute();
    }

    protected function createType($builder, $type, $value)
    {
        foreach ($value as $subKey => $subValue) {
            call_user_func_array(
                array($builder, $this->typeMap[$type]),
                array($this->processCriteria($builder->expr(), $subKey, $subValue))
            );
        }

        return $builder;
    }

    protected function createOperator($builder, $field, $operator, $value)
    {
        return call_user_func_array(
            array($builder->field($field), $this->operatorMap[$operator]),
            array($value)
        );
    }
}
