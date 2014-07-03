<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceManagerFactory
{
    protected $resources;

    protected $eventDispatcher;

    protected $classResolver;

    public function __construct(
        array $resources,
        EventDispatcherInterface $eventDispatcher,
        ResourceManagerClassResolver $classResolver = null
    ) {
        $this->resources = $resources;
        $this->eventDispatcher = $eventDispatcher;
        $this->classResolver = $classResolver ?: new ResourceManagerClassResolver();
    }

    public function create($resourceName, ObjectManager $manager)
    {
        $resource = $this->getResource($resourceName);

        if ($resource === null) {
            throw new \InvalidArgumentException(
                sprintf("Resource '%s' not defined.", $resourceName)
            );
        }

        if (!isset($resource['model'])) {
            throw new \InvalidArgumentException(
                sprintf("Model for resource '%s' not defined.", $resourceName)
            );
        }

        if (!isset($resource['alias'])) {
            $resource['alias'] = 'resource';
        }

        $managerClass = $this->classResolver->resolve($manager);

        return new $managerClass(
            $manager,
            $this->eventDispatcher,
            $resource['model'],
            $resourceName,
            $resource['alias']
        );
    }

    protected function getResource($resourceName)
    {
        return isset($this->resources[$resourceName]) ? $this->resources[$resourceName] : null;
    }
}
