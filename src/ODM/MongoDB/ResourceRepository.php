<?php

namespace Anh\DoctrineResource\ODM\MongoDB;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends DocumentRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    protected function getAdapterClass()
    {
        return 'Anh\DoctrineResource\ODM\MongoDB\QueryBuilderAdapter';
    }
}
