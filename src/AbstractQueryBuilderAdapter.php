<?php

namespace Anh\DoctrineResource;

/*
$criteria = [
    'section' => 'articles', // usual
    '#or' => [
        'isValid' => true,
        '%state' => [ 'not in' => [ 'moderated', 'approved' ]]
    ],
    '%rating' => [ '>' => 10 ]
    '#or-2' => [
        '%title-1' => [ 'like' => 'test%' ]
        '%title-2' => [ 'like' => '%test' ]
    ],

    "date_sub(resource.createdAt, 1, 'DAY') > 0",
];
 */

abstract class AbstractQueryBuilderAdapter implements QueryBuilderAdapterInterface
{
    protected $alias;

    protected $rules;

    protected $parameters;

    protected $typeMap;

    protected $operatorMap;

    public function __construct($alias, $rules)
    {
        $this->alias = $alias;
        $this->rules = $rules;
    }

    abstract public function applyCriteria($queryBuilder, array $criteria);

    abstract protected function createType($builder, $type, $value);
    abstract protected function createOperator($builder, $field, $operator, $value);
    abstract protected function createOrderBy($builder, $field, $order);

    public function applySorting($queryBuilder, array $sorting = null)
    {
        if (empty($sorting)) {
            return $this;
        }

        foreach ($sorting as $field => $order) {
            if ($this->fieldHasJoin($field)) {
                $this->createJoin($queryBuilder, $this->getJoin($field));
            }

            $this->createOrderBy($queryBuilder, $field, $order);
        }

        return $this;
    }

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

    /**
     * @param  string $name
     * @return string
     */
    protected function getFieldName($field)
    {
        if (strpos($field, '.') === false && $this->alias) {
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

    protected function isFieldLess($key)
    {
        return is_numeric($key);
    }

    protected function isRuleName($value)
    {
        return $value[0] === '[' && substr($value, -1) === ']';
    }

    protected function fieldHasJoin($field)
    {
        return strpos($field, '.') !== false;
    }

    protected function getJoin($field)
    {
        $parts = explode('.', $field);

        return reset($parts);
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

    protected function getRuleName($value)
    {
        return trim($value, '[]');
    }

    protected function getRule($rule)
    {
        if (isset($this->rules[$rule])) {
            return $this->rules[$rule];
        }

        throw new \Exception(
            sprintf("Rule '%s' not found.", $rule)
        );
    }

    protected function getDefaultOperator($value)
    {
        return is_array($value) ? 'in' : '=';
    }

    protected function getParameterName($field)
    {
        $number = 1;
        $name = str_replace('.', '_', $field);

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

        if ($this->isFieldLess($key)) {
            if ($this->isRuleName($value)) {
                $criteria = $this->getRule($this->getRuleName($value));

                return $this->processCriteria($builder, '#and', $criteria);
            }

            return $this->createRaw($value);
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

        if ($this->fieldHasJoin($field)) {
            $this->createJoin($builder, $this->getJoin($field));
        }

        if (!array_key_exists($operator, $this->operatorMap)) {
            throw new \Exception(
                sprintf("Unknown operator '%s'.", $operator)
            );
        }

        return $this->createOperator($builder, $field, $operator, $value);
    }

    protected function createRaw($value)
    {
        throw new \Exception(
            sprintf("Raw condition '%s' not supported.", $value)
        );
    }

    protected function createJoin($builder, $join)
    {
        throw new \Exception(
            sprintf("Joins not implemented for '%s'.", get_class($this))
        );
    }
}
