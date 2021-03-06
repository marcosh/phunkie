<?php

namespace spec\Phunkie\Types;

use Phunkie\Utils\Copiable;
use PhpSpec\ObjectBehavior;

class PairSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith("a", 1);
    }

    function it_is_copiable()
    {
        $this->shouldHaveType(Copiable::class);
    }
}