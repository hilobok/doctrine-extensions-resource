<?php

namespace Anh\DoctrineResource\ORM;

use Anh\DoctrineResource\ResourceManager as BaseResourceManager;

class ResourceManager extends BaseResourceManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $manager;

    public function delete($resource, $flush = true)
    {
        // using getReference() to prevent fetching whole entity from db
        // http://stackoverflow.com/questions/11486662/doctrine-entity-remove-vs-delete-query-performance-comparison
        if (is_numeric($resource)) {
            $resource = $this->manager->getReference($this->modelClass, $resource);
        }

        return parent::delete($resource, $flush);
    }
}
