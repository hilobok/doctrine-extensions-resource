<?php

namespace Anh\DoctrineResource\ODM\MongoDB;

use Doctrine\ODM\MongoDB\Repository\DefaultRepositoryFactory;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Doctrine MongoDB ODM don't have ability to specify custom repository factory (at least in 1.0.0-BETA10)
 * Was proposed in PR #892 https://github.com/doctrine/mongodb-odm/pull/892
 */
class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    public function __construct(/* ResourcePaginatorInterface */ $paginator)
    {
        $this->paginator = $paginator;
    }

    protected function createRepository(DocumentManager $documentManager, $documentName)
    {
        $repository = parent::createRepository($documentManager, $documentName);

        if ($repository instanceof ResourceRepository) {
            $repository->setPaginator($this->paginator);
        }

        return $repository;
    }
}
