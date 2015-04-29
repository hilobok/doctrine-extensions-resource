<?php

namespace spec\Anh\DoctrineResource\ORM;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceRepositoryFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Anh\DoctrineResource\ORM\ResourceRepositoryFactory');
    }
}
