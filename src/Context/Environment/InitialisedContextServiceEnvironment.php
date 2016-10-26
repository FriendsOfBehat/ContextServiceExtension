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

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Exception\ContextNotFoundException;
use Behat\Testwork\Call\Callee;
use Behat\Testwork\Suite\Suite;
use FriendsOfBehat\ContextServiceExtension\Context\Environment\Handler\ContextServiceEnvironmentHandler;

/**
 * @internal
 *
 * @see ContextServiceEnvironmentHandler
 */
final class InitialisedContextServiceEnvironment implements ContextServiceEnvironment
{
    /**
     * @var Suite
     */
    private $suite;

    /**
     * @var Context[]
     */
    private $contexts = [];

    /**
     * @param Suite $suite
     */
    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @param Context $context
     */
    public function registerContext(Context $context)
    {
        $this->contexts[get_class($context)] = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * {@inheritdoc}
     */
    public function bindCallee(Callee $callee)
    {
        $callable = $callee->getCallable();

        if ($callee->isAnInstanceMethod()) {
            return [$this->getContext($callable[0]), $callable[1]];
        }

        return $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContexts()
    {
        return count($this->contexts) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextClasses()
    {
        return array_keys($this->contexts);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContextClass($class)
    {
        return isset($this->contexts[$class]);
    }

    /**
     * @param string $class
     *
     * @return Context
     *
     * @throws ContextNotFoundException
     */
    private function getContext($class)
    {
        if (!isset($this->contexts[$class])) {
            throw new ContextNotFoundException(sprintf(
                '`%s` context is not found in the suite environment. Have you registered it?',
                $class
            ), $class);
        }

        return $this->contexts[$class];
    }
}
