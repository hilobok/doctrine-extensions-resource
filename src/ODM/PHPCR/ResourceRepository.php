<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends DocumentRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    protected $adapterClass = 'Anh\DoctrineResource\ODM\PHPCR\QueryBuilderAdapter';
}
