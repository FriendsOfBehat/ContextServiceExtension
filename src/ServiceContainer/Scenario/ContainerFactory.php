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

use ProxyManager\Configuration as ProxyManagerConfiguration;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @internal
 */
final class ContainerFactory
{
    /**
     * @param string $basePath
     * @param array $importedFiles
     *
     * @return ContainerBuilder
     */
    public function createContainer($basePath, array $importedFiles = [])
    {
        $container = new ContainerBuilder();

        $this->enableSupportForLazyServicesIfPossible($container);

        $loader = $this->createLoader($container, $basePath);
        foreach ($importedFiles as $file) {
            $loader->load($file);
        }

        return $container;
    }

    /**
     * @param ContainerBuilder $container
     * @param string $basePath
     *
     * @return DelegatingLoader
     */
    private function createLoader(ContainerBuilder $container, $basePath)
    {
        $fileLocator = new FileLocator($basePath);
        $loader = new DelegatingLoader(new LoaderResolver([
            new Loader\XmlFileLoader($container, $fileLocator),
            new Loader\YamlFileLoader($container, $fileLocator),
            new Loader\PhpFileLoader($container, $fileLocator),
        ]));

        return $loader;
    }

    /**
     * @param ContainerBuilder $container
     */
    private function enableSupportForLazyServicesIfPossible(ContainerBuilder $container)
    {
        if (class_exists(ProxyManagerConfiguration::class) && class_exists(RuntimeInstantiator::class)) {
            $container->setProxyInstantiator(new RuntimeInstantiator());
        }
    }
}
