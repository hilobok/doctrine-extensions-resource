<?php

namespace Anh\DoctrineResource\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends EntityRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    protected $adapterClass = 'Anh\DoctrineResource\ORM\QueryBuilderAdapter';
}
