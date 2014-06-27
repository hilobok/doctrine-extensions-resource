<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

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
    );

    public function applyCriteria($queryBuilder, array $criteria = null)
    {
        if (empty($criteria)) {
            return;
        }

        $this->parameters = array();
        $this->processCriteria($queryBuilder->where(), '#and', $criteria);

        if (!empty($this->parameters)) {
            foreach ($this->parameters as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }
        }

        return $this;
    }

    public function applySorting($queryBuilder, array $sorting = null)
    {
        if (empty($sorting)) {
            return;
        }

        foreach ($sorting as $field => $order) {
            $queryBuilder->addOrderBy()->{$order}()->field($this->getFieldName($field));
        }

        return $this;
    }

    protected function getDefaultOperator($value)
    {
        return '=';
    }

    protected function createType($builder, $type, $value)
    {
        $builder = call_user_func(array($builder, $this->typeMap[$type]));

        foreach ($value as $subKey => $subValue) {
            $builder = $this->processCriteria($builder, $subKey, $subValue);
        }

        return $builder->end();
    }

    protected function createOperator($builder, $field, $operator, $value)
    {
        $parameterName = $this->getParameterName($field);

        $builder = call_user_func(array($builder, $this->operatorMap[$operator]))
            ->field($this->getFieldName($field))
            ->parameter($parameterName)
        ;

        $this->parameters[$parameterName] = $value;

        return $builder;
    }
}
