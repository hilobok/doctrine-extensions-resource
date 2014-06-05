<?php

namespace spec\Anh\DoctrineResource;

use PhpSpec\ObjectBehavior;
use Doctrine\ORM\EntityManager as ORMObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceManagerFactorySpec extends ObjectBehavior
{
    public function let(ORMObjectManager $manager, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith(
            array(
                'article' => array(
                    'model' => 'Article/Entity'
                ),
                'car' => array(
                )
            ),
            $manager,
            $eventDispatcher
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Anh\DoctrineResource\ResourceManagerFactory');
    }

    public function it_should_create_manager()
    {
        $this->create('article')->shouldHaveType('Anh\DoctrineResource\ResourceManager');
    }

    public function it_should_throw_exception_on_undefined_resource()
    {
        $this->shouldThrow('InvalidArgumentException')
            ->during('create', array('blog'))
        ;
    }

    public function it_should_throw_exception_on_resource_without_model()
    {
        $this->shouldThrow('InvalidArgumentException')
            ->during('create', array('car'))
        ;
    }
}
