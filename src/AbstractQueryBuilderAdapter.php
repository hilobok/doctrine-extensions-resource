<?php

namespace Anh\DoctrineResource;

/*
$criteria = [
    'section' => 'articles', // usual
    '#or' => [
        'isValid' => true,
        '%state' => [ 'not in' => [ 'moderated', 'approved' ]]
    ]
    '%rating' => [ '>' => 10 ]
    '#or-2' => [
        '%title-1' => [ 'like' => 'test%' ]
        '%title-2' => [ 'like' => '%test' ]
    ]
];
 */

abstract class AbstractQueryBuilderAdapter implements QueryBuilderAdapterInterface
{
    protected $alias;

    protected $parameters;

    protected $typeMap;

    protected $operatorMap;

    abstract public function applyCriteria($queryBuilder, array $criteria);
    abstract public function applySorting($queryBuilder, array $sorting);

    abstract protected function createType($builder, $type, $value);
    abstract protected function createOperator($builder, $field, $operator, $value);

    public function applyLimit($queryBuilder, $limit = null)
    {
        if (empty($limit)) {
            return $this;
        }

        $queryBuilder->setMaxResults($limit);

        return $this;
    }

    public function applyOffset($queryBuilder, $offset = null)
    {
        if ($offset === null) {
            return $this;
        }

        $queryBuilder->setFirstResult($offset);

        return $this;
    }

    public function getResult($queryBuilder)
    {
        return $queryBuilder->getQuery()->getResult();
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function getFieldName($field)
    {
        if (false === strpos($field, '.') && $this->alias) {
            return sprintf('%s.%s', $this->alias, $field);
        }

        return $field;
    }

    protected function isType($key)
    {
        return $key[0] === '#';
    }

    protected function isField($key)
    {
        return $key[0] === '%';
    }

    protected function getType($key)
    {
        if (!preg_match('/#(?P<type>and|or)(-\d+)?$/i', $key, $match)) {
            throw new \Exception(
                "Condition type should be 'and' or 'or'."
            );
        }

        return $match['type'];
    }

    protected function getField($key)
    {
        if (!preg_match('/%(?P<field>[a-z_\d]+)(-\d+)?$/i', $key, $match)) {
            throw new \Exception(
                sprintf("Unable to get field from '%s'.", $key)
            );
        }

        return $match['field'];
    }

    protected function getDefaultOperator($value)
    {
        return is_array($value) ? 'in' : '=';
    }

    protected function getParameterName($field)
    {
        $number = 1;
        $name = $field;

        while (array_key_exists($name, $this->parameters)) {
            $name = sprintf('%s%d', $field, $number);
            $number++;
        }

        return $name;
    }

    protected function processCriteria($builder, $key, $value)
    {
        if ($this->isType($key)) {
            $type = $this->getType($key);

            if (!is_array($value)) {
                throw new \Exception(
                    'Malformed condition type.'
                );
            }

            return $this->createType($builder, $type, $value);
        }

        if ($this->isField($key)) {
            $field = $this->getField($key);

            if (!is_array($value) || count($value) != 1) {
                throw new \Exception(
                    'Malformed condition.'
                );
            }

            $operator = key($value);
            $value = current($value);
        } else {
            $operator = $this->getDefaultOperator($value);
            $field = $key;
        }

        if (!array_key_exists($operator, $this->operatorMap)) {
            throw new \Exception(
                sprintf("Unknown operator '%s'.", $operator)
            );
        }

        return $this->createOperator($builder, $field, $operator, $value);
    }
}
