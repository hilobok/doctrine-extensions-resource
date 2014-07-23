<?php

namespace Anh\DoctrineResource\ORM;

use Anh\DoctrineResource\AbstractQueryBuilderAdapter;

class QueryBuilderAdapter extends AbstractQueryBuilderAdapter
{
    protected $typeMap = array(
        'and' => 'andX',
        'or' => 'orX',
    );

    protected $operatorMap = array(
        '=' => 'eq',
        '==' => 'eq',
        '<>' => 'neq',
        '!=' => 'neq',
        '>' => 'gt',
        '>=' => 'gte',
        '<' => 'lt',
        '<=' => 'lte',
        'like' => 'like',
        'not like' => 'notLike',
        'in' => 'in',
        'not in' => 'notIn',
        'is null' => 'isNull',
        'is not null' => 'isNotNull',
    );

    protected $singleOperandOperators = array(
        'is null',
        'is not null',
    );

    public function applyCriteria($queryBuilder, array $criteria = null)
    {
        if (empty($criteria)) {
            return $this;
        }

        $this->parameters = array();

        $queryBuilder->where(
            $this->processCriteria($queryBuilder, '#and', $criteria)
        );

        if (!empty($this->parameters)) {
            $queryBuilder->setParameters($this->parameters);
        }

        return $this;
    }

    protected function createOrderBy($builder, $field, $order)
    {
        return $builder->orderBy($this->getFieldName($field), $order);
    }

    protected function createType($builder, $type, $value)
    {
        $params = array();

        foreach ($value as $subKey => $subValue) {
            $params[] = $this->processCriteria($builder, $subKey, $subValue);
        }

        return call_user_func_array(
            array($builder->expr(), $this->typeMap[$type]),
            $params
        );
    }

    protected function createOperator($builder, $field, $operator, $value)
    {
        if ($this->operatorMap[$operator] == 'eq' && is_null($value)) {
            $operator = 'is null';
        }

        if (in_array($operator, $this->singleOperandOperators, true)) {
            $params = array($this->getFieldName($field));
        } else {
            $parameterName = $this->getParameterName($field);
            $params = array($this->getFieldName($field), sprintf(':%s', $parameterName));
            $this->parameters[$parameterName] = $value;
        }

        return call_user_func_array(
            array($builder->expr(), $this->operatorMap[$operator]),
            $params
        );
    }

    protected function createRaw($value)
    {
        return $value;
    }

    protected function createJoin($builder, $join)
    {
        return $this->builderHasJoin($builder, $join)
            ? $builder
            : $builder->join($this->getFieldName($join), $join)
        ;
    }

    protected function builderHasJoin($builder, $join)
    {
        $joins = $builder->getDqlPart('join');

        if (isset($joins[$this->alias]) && is_array($joins[$this->alias])) {
            foreach ($joins[$this->alias] as $expr) {
                if ($expr->getAlias() == $join) {
                    return true;
                }
            }
        }

        return false;
    }
}
