<?php

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) FriendsOfBehat
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfBehat\ContextServiceExtension\ServiceContainer;

use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\ContextServiceExtension\Context\ContextRegistry;
use FriendsOfBehat\ContextServiceExtension\Context\Environment\Handler\ContextServiceEnvironmentHandler;
use FriendsOfBehat\ContextServiceExtension\ServiceContainer\Scenario\ContainerFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class ContextServiceExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'fob_context_service';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('imports')
                    ->performNoDeepMerging()
                    ->prototype('scalar')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadContextRegistry($container);
        $this->loadScenarioServiceContainer($container, $config);
        $this->loadEnvironmentHandler($container);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {

    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadContextRegistry(ContainerBuilder $container)
    {
        $container->setDefinition('fob_context_service.context_registry', (new Definition(ContextRegistry::class))->setPublic(false));
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function loadScenarioServiceContainer(ContainerBuilder $container, array $config)
    {
        $container->set('fob_context_service.service_container.scenario', (new ContainerFactory())->createContainer(
            $container->getParameter('paths.base'),
            $container->getDefinition('fob_context_service.context_registry'),
            $config['imports']
        ));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadEnvironmentHandler(ContainerBuilder $container)
    {
        $definition = new Definition(ContextServiceEnvironmentHandler::class, [
            new Reference('fob_context_service.service_container.scenario'),
            new Reference('fob_context_service.context_registry'),
        ]);
        $definition->addTag(EnvironmentExtension::HANDLER_TAG, ['priority' => 128]);

        $container->setDefinition('fob_context_service.environment_handler.context_service', $definition);
    }
}
