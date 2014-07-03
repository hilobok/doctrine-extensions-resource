<?php

namespace Anh\DoctrineResource;

class RuleResolver
{
    protected $rules;

    public function add($resource, $rule, array $criteria)
    {
        $this->rules[$resource][$rule] = $criteria;

        return $this;
    }

    public function resolve($resource, $rule)
    {
        return isset($this->rules[$resource][$rule])
            ? $this->rules[$resource][$rule]
            : array()
        ;
    }
}
