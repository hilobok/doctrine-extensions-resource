<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anh\DoctrineResource\Event\ResourceEvent;

class ResourceManager
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var string
     */
    protected $resourceAlias;

    protected $preEvents = array(
        'create' => ResourceEvent::PRE_CREATE,
        'update' => ResourceEvent::PRE_UPDATE,
        'delete' => ResourceEvent::PRE_DELETE,
    );

    protected $postEvents = array(
        'create' => ResourceEvent::POST_CREATE,
        'update' => ResourceEvent::POST_UPDATE,
        'delete' => ResourceEvent::POST_DELETE,
    );

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $modelClass, $resourceName, $resourceAlias)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->modelClass = $modelClass;
        $this->resourceName = $resourceName;
        $this->resourceAlias = $resourceAlias;
    }

    public function createResource()
    {
        return new $this->modelClass();
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getRepository()
    {
        $repository = $this->manager->getRepository($this->modelClass);

        if ($repository instanceof ResourceRepositoryInterface) {
            $repository
                ->setResourceName($this->resourceName)
                ->setResourceAlias($this->resourceAlias)
            ;
        }

        return $repository;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function create($resource, $flush = true)
    {
        return $this->perform('create', $resource, $flush);
    }

    public function update($resource, $flush = true)
    {
        return $this->perform('update', $resource, $flush);
    }

    public function delete($resource, $flush = true)
    {
        return $this->perform('delete', $resource, $flush);
    }

    protected function perform($command, $resource, $flush)
    {
        if (is_array($resource)) {
            return $this->performMultiple($command, $resource, $flush);
        }

        $event = $this->dispatchEvent($this->preEvents[$command], $resource);

        if ($event->isPropagationStopped()) {
            return;
        }

        switch ($command) {
            case 'create':
            case 'update':
                $this->manager->persist($resource);
                break;

            case 'delete':
                $this->manager->remove($resource);
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf("Unknown command '%s'.", $command)
                );
        }

        if ($flush) {
            $this->manager->flush();
        }

        $this->dispatchEvent($this->postEvents[$command], $resource);

        return $resource;
    }

    protected function performMultiple($command, array $resources, $flush)
    {
        $result = array();

        foreach ($resources as $key => $resource) {
            $result[$key] = $this->perform($command, $resource, false);
        }

        if ($flush) {
            $this->manager->flush();
        }

        return $result;
    }

    protected function dispatchEvent($eventName, $resource)
    {
        return $this->eventDispatcher->dispatch(
            $this->getEventName($eventName),
            new ResourceEvent($resource)
        );
    }

    protected function getEventName($eventName)
    {
        return sprintf('%s.%s.%s', 'anh_resource', $this->resourceName, $eventName);
    }
}
