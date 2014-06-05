<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager as ORMObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager as MongoDBObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager as PHPCRObjectManager;

class ResourceManagerClassResolver
{
    protected $managerClasses = array(
        'orm' => 'Anh\DoctrineResource\ORM\ResourceManager',
        'mongodb' => 'Anh\DoctrineResource\ODM\MongoDB\ResourceManager',
        'phpcr' => 'Anh\DoctrineResource\ODM\PHPCR\ResourceManager'
    );

    public function resolve(ObjectManager $manager)
    {
        switch (true) {
            case ($manager instanceof ORMObjectManager):
                return $this->managerClasses['orm'];
                break;

            case ($manager instanceof MongoDBObjectManager):
                return $this->managerClasses['mongodb'];
                break;

            case ($manager instanceof PHPCRObjectManager):
                return $this->managerClasses['phpcr'];
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf("Unable to resolve manager class for '%s'.", get_class($manager))
                );
                break;
        }
    }

    public function setClass($type, $class)
    {
        $this->managerClasses[$type] = $class;
    }
}
