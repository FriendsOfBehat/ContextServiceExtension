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

namespace FriendsOfBehat\ContextServiceExtension\ServiceContainer\Scenario;

use ProxyManager\Configuration as ProxyManagerConfiguration;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
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
    public function createContainer(string $basePath, array $importedFiles = []): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $this->enableSupportForLazyServicesIfPossible($container);

        $loader = $this->createLoader($container, $basePath);
        foreach ($importedFiles as $file) {
            $type = false !== mb_strpos($file, '*') ? 'glob' : null;
            $loader->load($file, $type);
        }

        return $container;
    }

    /**
     * @param ContainerBuilder $container
     * @param string $basePath
     *
     * @return LoaderInterface
     */
    private function createLoader(ContainerBuilder $container, string $basePath): LoaderInterface
    {
        $fileLocator = new FileLocator($basePath);
        $loader = new DelegatingLoader(new LoaderResolver([
            new Loader\XmlFileLoader($container, $fileLocator),
            new Loader\YamlFileLoader($container, $fileLocator),
            new Loader\PhpFileLoader($container, $fileLocator),
            new Loader\GlobFileLoader($container, $fileLocator)
        ]));

        return $loader;
    }

    /**
     * @param ContainerBuilder $container
     */
    private function enableSupportForLazyServicesIfPossible(ContainerBuilder $container): void
    {
        if (class_exists(ProxyManagerConfiguration::class) && class_exists(RuntimeInstantiator::class)) {
            $container->setProxyInstantiator(new RuntimeInstantiator());
        }
    }
}
