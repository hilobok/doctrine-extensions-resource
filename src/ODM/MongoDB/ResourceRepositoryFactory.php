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
