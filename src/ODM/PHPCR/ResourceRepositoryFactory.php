<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

use Doctrine\ODM\PHPCR\Repository\DefaultRepositoryFactory;
use Doctrine\ODM\PHPCR\DocumentManager;
use Anh\DoctrineResource\RuleResolver;

class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    protected $paginator;

    protected $ruleResolver;

    /**
     * Constructor
     * @param mixed $paginator Paginator, should be compatible with ResourcePaginatorInterface.
     */
    public function __construct($paginator, RuleResolver $ruleResolver = null)
    {
        $this->paginator = $paginator;
        $this->ruleResolver = $ruleResolver ?: new RuleResolver();
    }

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function createRepository(DocumentManager $documentManager, $documentName)
    {
        $repository = parent::createRepository($documentManager, $documentName);

        if ($repository instanceof ResourceRepository) {
            $repository
                ->setPaginator($this->paginator)
                ->setRuleResolver($this->ruleResolver)
            ;
        }

        return $repository;
    }
}
