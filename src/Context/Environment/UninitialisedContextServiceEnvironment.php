<?php

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
     * @var array
     */
    private $contextServices = [];

    /**
     * @param string $serviceId
     * @param string $serviceClass
     */
    public function registerContextService($serviceId, $serviceClass)
    {
        $this->contextServices[$serviceId] = $serviceClass;
    }

    /**
     * @return array
     */
    public function getContextServices()
    {
        return array_keys($this->contextServices);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContexts()
    {
        return count($this->contextServices) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextClasses()
    {
        return array_values($this->contextServices);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContextClass($class)
    {
        return in_array($class, $this->contextServices, true);
    }
}
