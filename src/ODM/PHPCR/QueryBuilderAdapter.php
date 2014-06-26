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

    public function buildCriteria($builder, array $criteria)
    {
        $this->parameters = array();
        $this->processCriteria($builder->where(), '#and', $criteria);

        if (!empty($this->parameters)) {
            foreach ($this->parameters as $name => $value) {
                $builder->setParameter($name, $value);
            }
        }
    }

    public function buildSorting($builder, array $sorting)
    {
        foreach ($sorting as $field => $order) {
            $builder->addOrderBy()->{$order}()->field($this->getFieldName($field));
        }
    }

    public function buildLimit($builder, $limit)
    {
        $builder->setMaxResults($limit);
    }

    public function buildOffset($builder, $offset)
    {
        $builder->setFirstResult($offset);
    }

    public function getResult($builder)
    {
        return $builder->getQuery()->getResult();
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
