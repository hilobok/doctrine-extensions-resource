<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

use Doctrine\ODM\PHPCR\Repository\DefaultRepositoryFactory;
use Doctrine\ODM\PHPCR\DocumentManager;

class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    protected $paginator;

    /**
     * Constructor
     * @param mixed $paginator Paginator, should be compatible with ResourcePaginatorInterface.
     */
    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function createRepository(DocumentManager $documentManager, $documentName)
    {
        $repository = parent::createRepository($documentManager, $documentName);

        if ($repository instanceof ResourceRepository) {
            $repository->setPaginator($this->paginator);
        }

        return $repository;
    }
}
