Feature: Registering contexts services in PHP
    In order to allow my contexts to be created using Symfony DI
    As a Behat Developer
    I want to be able to define them as services

    Background:
        Given a context file "features/bootstrap/FeatureContext.php" containing:
        """
        <?php

        use Behat\Behat\Context\Context;

        class FeatureContext implements Context
        {
            private $parameter;

            public function __construct($parameter)
            {
                $this->parameter = $parameter;
            }

            /**
             * @Given the parameter was injected to the context
             */
            public function theParameterWasInjectedToTheContext()
            {
                if (null === $this->parameter) {
                    throw new \DomainException('No parameter was injected (or null one)!');
                }
            }

            /**
             * @When I change it to :content
             */
             public function iChangeItTo($content)
             {
                 $this->parameter = $content;
             }

            /**
             * @Then it should contain :content
             */
             public function itShouldContain($content)
             {
                 if ($content !== $this->parameter) {
                    throw new \DomainException(sprintf('Expected to get "%s", got "%s"!', $content, $this->parameter));
                 }
             }
        }
        """
        And a Behat configuration containing:
        """
        default:
            suites:
                default:
                    contexts_services:
                        - feature_context

            extensions:
                FriendsOfBehat\ContextServiceExtension:
                    imports:
                        - features/bootstrap/config/services.php
        """
        And a config file "features/bootstrap/config/services.php" containing:
        """
        <?php

        use Symfony\Component\DependencyInjection\Definition;
        use Symfony\Component\DependencyInjection\Parameter;

        $container->setParameter('foobar', 'shit happens');

        $contextDefinition = new Definition(\FeatureContext::class, ['%foobar%']);
        $contextDefinition->addTag('fob.context_service');
        $container->setDefinition('feature_context', $contextDefinition);
        """

    Scenario: Using registered context service
        Given a feature file "features/registering_context_service.feature" containing:
        """
        Feature: Registering context service

            Scenario:
                Given the parameter was injected to the context
                Then it should contain "shit happens"
        """
        When I run Behat
        Then it should pass

    Scenario: New context service instance is used for each scenario
        Given a feature file "features/registering_context_service.feature" containing:
        """
        Feature: Registering context service

            Scenario:
                Given the parameter was injected to the context
                When I change it to "shit does not happen"
                Then it should contain "shit does not happen"

            Scenario:
                Given the parameter was injected to the context
                Then it should contain "shit happens"
        """
        When I run Behat
        Then it should pass
