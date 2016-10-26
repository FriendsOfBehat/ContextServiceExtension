<?php

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) Kamil Kokot <kamil@kokot.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfBehat\ContextServiceExtension\ServiceContainer\Scenario;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 */
final class ContextRegistryPass implements CompilerPassInterface
{
    /**
     * @var Definition
     */
    private $contextRegistryDefinition;

    /**
     * @param Definition $contextRegistryDefinition
     */
    public function __construct(Definition $contextRegistryDefinition)
    {
        $this->contextRegistryDefinition = $contextRegistryDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('fob.context_service');

        foreach ($taggedServices as $id => $tags) {
            $this->contextRegistryDefinition->addMethodCall(
                'add',
                [$id, $container->findDefinition($id)->getClass()]
            );
        }
    }
}
