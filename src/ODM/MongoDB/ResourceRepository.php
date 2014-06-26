<?php

namespace Anh\DoctrineResource\ODM\MongoDB;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends DocumentRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    protected $adapterClass = 'Anh\DoctrineResource\ODM\MongoDB\QueryBuilderAdapter';
}
