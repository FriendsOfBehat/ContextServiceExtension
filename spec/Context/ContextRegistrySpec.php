<?php

declare(strict_types=1);

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) Kamil Kokot <kamil@kokot.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FriendsOfBehat\ContextServiceExtension\Context;

use PhpSpec\ObjectBehavior;

final class ContextRegistrySpec extends ObjectBehavior
{
    function it_stores_class_by_id(): void
    {
        $this->add('context_id', 'context_class');

        $this->getClass('context_id')->shouldReturn('context_class');
    }

    function it_throws_an_exception_if_trying_to_get_class_by_unexisting_id(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getClass', ['context_id']);
    }
}
