<?php

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) Kamil Kokot <kamil@kokot.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfBehat\ContextServiceExtension\ServiceContainer;

use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\ContextServiceExtension\Context\ContextRegistry;
use FriendsOfBehat\ContextServiceExtension\Context\Environment\Handler\ContextServiceEnvironmentHandler;
use FriendsOfBehat\ContextServiceExtension\Listener\ScenarioContainerResetter;
use FriendsOfBehat\ContextServiceExtension\ServiceContainer\Scenario\ContainerFactory;
use FriendsOfBehat\ContextServiceExtension\ServiceContainer\Scenario\ContextRegistryPass;
use FriendsOfBehat\CrossContainerExtension\CrossContainerProcessor;
use FriendsOfBehat\CrossContainerExtension\ServiceContainer\CrossContainerExtension;
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
     * @var CrossContainerProcessor|null
     */
    private $crossContainerProcessor;

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
        /** @var CrossContainerExtension|null $crossContainerExtension */
        $crossContainerExtension = $extensionManager->getExtension('fob_cross_container');
        if (null !== $crossContainerExtension) {
            $this->crossContainerProcessor = $crossContainerExtension->getCrossContainerProcessor();
        }
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
        /** @var ContainerBuilder $scenarioContainer */
        $scenarioContainer = $container->get('fob_context_service.service_container.scenario');

        if (null !== $this->crossContainerProcessor) {
            $this->crossContainerProcessor->process($scenarioContainer);
        }

        $scenarioContainer->addCompilerPass(new ContextRegistryPass($container->getDefinition('fob_context_service.context_registry')));
        $scenarioContainer->compile();
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
        $container->set(
            'fob_context_service.service_container.scenario',
            (new ContainerFactory())->createContainer($container->getParameter('paths.base'), $config['imports'])
        );


        $definition = new Definition(ScenarioContainerResetter::class, [
            new Reference('fob_context_service.service_container.scenario'),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);
        $container->setDefinition('fob_context_service.service_container.scenario.resetter', $definition);
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
