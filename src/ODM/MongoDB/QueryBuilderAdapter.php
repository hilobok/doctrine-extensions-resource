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
    );

    public function buildCriteria($builder, array $criteria)
    {
        $this->parameters = array();
        $this->processCriteria($builder, '#and', $criteria);
    }

    public function buildSorting($builder, array $sorting)
    {
        foreach ($sorting as $field => $order) {
            $builder->sort($field, $order);
        }
    }

    public function buildLimit($builder, $limit)
    {
        $builder->limit($limit);
    }

    public function buildOffset($builder, $offset)
    {
        $builder->skip($offset);
    }

    public function getResult($builder)
    {
        return $builder->getQuery()->execute();
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
