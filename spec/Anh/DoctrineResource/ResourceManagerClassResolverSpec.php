<?php

namespace spec\Anh\DoctrineResource;

use PhpSpec\ObjectBehavior;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager as ORMObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager as MongoDBObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager as PHPCRObjectManager;

class ResourceManagerClassResolverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Anh\DoctrineResource\ResourceManagerClassResolver');
    }

    public function it_should_resolve_orm(ORMObjectManager $manager)
    {
        $this->resolve($manager)->shouldReturn('Anh\DoctrineResource\ORM\ResourceManager');
    }

    public function it_should_resolve_mongodb(MongoDBObjectManager $manager)
    {
        $this->resolve($manager)->shouldReturn('Anh\DoctrineResource\ODM\MongoDB\ResourceManager');
    }

    public function it_should_resolve_phpcr(PHPCRObjectManager $manager)
    {
        $this->resolve($manager)->shouldReturn('Anh\DoctrineResource\ODM\PHPCR\ResourceManager');
    }

    public function it_should_set_class(ORMObjectManager $manager)
    {
        $this->setClass('orm', 'Some\Another\ResourceManagerClass');
        $this->resolve($manager)->shouldReturn('Some\Another\ResourceManagerClass');
    }

    public function it_should_throw_exception_on_unknown_manager(ObjectManager $manager)
    {
        $this->shouldThrow('InvalidArgumentException')
            ->during('resolve', array($manager))
        ;
    }
}
