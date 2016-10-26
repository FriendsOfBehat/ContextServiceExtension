<?php

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) Kamil Kokot <kamil@kokot.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfBehat\ContextServiceExtension\Context\Environment\Handler;

use Behat\Behat\Context\Context;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Environment\Exception\EnvironmentIsolationException;
use Behat\Testwork\Environment\Handler\EnvironmentHandler;
use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use Behat\Testwork\Suite\Suite;
use FriendsOfBehat\ContextServiceExtension\Context\ContextRegistry;
use FriendsOfBehat\ContextServiceExtension\Context\Environment\InitialisedContextServiceEnvironment;
use FriendsOfBehat\ContextServiceExtension\Context\Environment\UninitialisedContextServiceEnvironment;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 */
final class ContextServiceEnvironmentHandler implements EnvironmentHandler
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ContextRegistry
     */
    private $contextRegistry;

    /**
     * @param ContainerInterface $container
     * @param ContextRegistry $contextRegistry
     */
    public function __construct(ContainerInterface $container, ContextRegistry $contextRegistry)
    {
        $this->container = $container;
        $this->contextRegistry = $contextRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsSuite(Suite $suite)
    {
        return $suite->hasSetting('contexts_services');
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnvironment(Suite $suite)
    {
        $environment = new UninitialisedContextServiceEnvironment($suite);
        foreach ($this->getSuiteContextsServices($suite) as $serviceId) {
            $environment->registerContextService($serviceId, $this->contextRegistry->getClass($serviceId));
        }

        return $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEnvironmentAndSubject(Environment $environment, $testSubject = null)
    {
        return $environment instanceof UninitialisedContextServiceEnvironment;
    }

    /**
     * {@inheritdoc}
     *
     * @throws EnvironmentIsolationException
     */
    public function isolateEnvironment(Environment $uninitializedEnvironment, $testSubject = null)
    {
        /** @var UninitialisedContextServiceEnvironment $uninitializedEnvironment */
        $this->assertEnvironmentCanBeIsolated($uninitializedEnvironment, $testSubject);

        $environment = new InitialisedContextServiceEnvironment($uninitializedEnvironment->getSuite());
        foreach ($uninitializedEnvironment->getContextServices() as $serviceId) {
            /** @var Context $context */
            $context = $this->container->get($serviceId);
            $environment->registerContext($context);
        }

        return $environment;
    }

    /**
     * @param Suite $suite
     *
     * @return string[]
     *
     * @throws SuiteConfigurationException If "contexts_services" setting is not an array
     */
    private function getSuiteContextsServices(Suite $suite)
    {
        $contextsServices = $suite->getSetting('contexts_services');

        if (!is_array($contextsServices)) {
            throw new SuiteConfigurationException(sprintf(
                '"contexts_services" setting of the "%s" suite is expected to be an array, %s given.',
                $suite->getName(),
                gettype($contextsServices)
            ), $suite->getName());
        }

        return $contextsServices;
    }

    /**
     * @param Environment $uninitializedEnvironment
     * @param mixed $testSubject
     *
     * @throws EnvironmentIsolationException
     */
    private function assertEnvironmentCanBeIsolated(Environment $uninitializedEnvironment, $testSubject)
    {
        if (!$this->supportsEnvironmentAndSubject($uninitializedEnvironment, $testSubject)) {
            throw new EnvironmentIsolationException(sprintf(
                '"%s" does not support isolation of "%s" environment.',
                static::class,
                get_class($uninitializedEnvironment)
            ), $uninitializedEnvironment);
        }
    }
}
