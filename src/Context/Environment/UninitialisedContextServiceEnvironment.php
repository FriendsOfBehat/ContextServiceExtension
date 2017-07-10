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

namespace FriendsOfBehat\ContextServiceExtension\Context\Environment;

use Behat\Testwork\Environment\StaticEnvironment;
use FriendsOfBehat\ContextServiceExtension\Context\Environment\Handler\ContextServiceEnvironmentHandler;

/**
 * @internal
 *
 * @see ContextServiceEnvironmentHandler
 */
final class UninitialisedContextServiceEnvironment extends StaticEnvironment implements ContextServiceEnvironment
{
    /**
     * @var string[]
     */
    private $contextServices = [];

    /**
     * @param string $serviceId
     * @param string $serviceClass
     */
    public function registerContextService(string $serviceId, string $serviceClass): void
    {
        $this->contextServices[$serviceId] = $serviceClass;
    }

    /**
     * @return array
     */
    public function getContextServices(): array
    {
        return array_keys($this->contextServices);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContexts(): bool
    {
        return count($this->contextServices) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextClasses(): array
    {
        return array_values($this->contextServices);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContextClass($class): bool
    {
        return in_array($class, $this->contextServices, true);
    }
}
