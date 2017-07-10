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

namespace FriendsOfBehat\ContextServiceExtension\Context;

/**
 * @internal
 */
final class ContextRegistry
{
    /**
     * @var string[]
     */
    private $registry;

    /**
     * @param string $serviceId
     * @param string $serviceClass
     */
    public function add(string $serviceId, string $serviceClass): void
    {
        $this->registry[$serviceId] = $serviceClass;
    }

    /**
     * @param string $serviceId
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getClass(string $serviceId): string
    {
        if (!isset($this->registry[$serviceId])) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find class for service with id "%s".',
                $serviceId
            ));
        }

        return $this->registry[$serviceId];
    }
}
