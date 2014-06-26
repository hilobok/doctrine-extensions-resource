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

    public function buildCriteria($builder, array $criteria)
    {
        $this->parameters = array();

        $builder->where(
            $this->processCriteria($builder, '#and', $criteria)
        );

        if (!empty($this->parameters)) {
            $builder->setParameters($this->parameters);
        }
    }

    public function buildSorting($builder, array $sorting)
    {
        foreach ($sorting as $field => $order) {
            $builder->orderBy($this->getFieldName($field), $order);
        }
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
}
