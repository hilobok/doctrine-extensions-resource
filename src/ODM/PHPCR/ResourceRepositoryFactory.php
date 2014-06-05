<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

use Doctrine\ODM\PHPCR\Repository\DefaultRepositoryFactory;
use Doctrine\ODM\PHPCR\DocumentManager;

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
