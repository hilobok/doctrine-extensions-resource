<?php

namespace Anh\DoctrineResource;

interface QueryBuilderAdapterInterface
{
    public function applyCriteria($queryBuilder, array $criteria);
    public function applySorting($queryBuilder, array $sorting);
    public function applyLimit($queryBuilder, $limit);
    public function applyOffset($queryBuilder, $offset);
    public function getResult($queryBuilder);
}