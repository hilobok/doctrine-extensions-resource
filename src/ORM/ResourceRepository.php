<?php

namespace Anh\DoctrineResource\ORM;

use Doctrine\ORM\EntityRepository;
use Anh\DoctrineResource\ResourceRepositoryInterface;
use Anh\DoctrineResource\ResourceRepositoryTrait;

class ResourceRepository extends EntityRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait;

    protected function getAdapterClass()
    {
        return 'Anh\DoctrineResource\ORM\QueryBuilderAdapter';
    }
}
