<?php

/*
 * This file is part of the ContextServiceExtension package.
 *
 * (c) Kamil Kokot <kamil@kokot.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfBehat\ContextServiceExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class ScenarioContainerResetter implements EventSubscriberInterface
{
    /**
     * @var ResettableContainerInterface
     */
    private $scenarioContainer;

    /**
     * @param ResettableContainerInterface $scenarioContainer
     */
    public function __construct(ResettableContainerInterface $scenarioContainer)
    {
        $this->scenarioContainer = $scenarioContainer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::AFTER => ['reset', -15],
            ExampleTested::AFTER => ['reset', -15],
        ];
    }

    public function reset()
    {
        $this->scenarioContainer->reset();
    }
}
