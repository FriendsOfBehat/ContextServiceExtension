<?php

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) FriendsOfBehat
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfBehat\ContextServiceExtension\ServiceContainer\Scenario;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @internal
 */
final class ContainerFactory
{
    /**
     * @param string $basePath
     * @param Definition $contextRegistryDefinition
     * @param array $importedFiles
     *
     * @return ContainerInterface
     */
    public function createContainer($basePath, Definition $contextRegistryDefinition, array $importedFiles = [])
    {
        $container = new ContainerBuilder();

        $loader = $this->createLoader($container, $basePath);
        foreach ($importedFiles as $file) {
            $loader->load($file);
        }

        $container->addCompilerPass(new ContextRegistryPass($contextRegistryDefinition));
        $container->compile();

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
}
