Feature: Registering contexts services using glob patterns
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
            private $foobar;
            private $qux;
            private $doo;

            public function __construct($foobar, $qux, $doo)
            {
                $this->foobar = $foobar;
                $this->qux = $qux;
                $this->doo = $doo;
            }

            /**
             * @Given parameters were injected to the context
             */
            public function parametersWereInjectedToTheContext()
            {
                $params = [
                    'foobar' => $this->foobar,
                    'qux' => $this->qux,
                    'doo' => $this->doo
                ];

                foreach ($params as $name => $value) {
                    if (null === $value) {
                        throw new \DomainException(sprintf(
                            'Parameter "%s" was not injected (or is null)!',
                            $name
                        ));
                    }
                }
            }

            /**
             * @When I change them to :a, :b, and :c
             */
             public function iChangeThemTo($a, $b, $c)
             {
                 $this->foobar = $a;
                 $this->qux = $b;
                 $this->doo = $c;
             }

            /**
             * @Then they should contain :a, :b, and :c
             */
             public function theyShouldContain($a, $b, $c)
             {
                 if ($a !== $this->foobar) {
                    throw new \DomainException(sprintf('Expected to get "%s", got "%s"!', $a, $this->foobar));
                 }

                 if ($b !== $this->qux) {
                    throw new \DomainException(sprintf('Expected to get "%s", got "%s"!', $b, $this->qux));
                 }

                 if ($c !== $this->doo) {
                    throw new \DomainException(sprintf('Expected to get "%s", got "%s"!', $c, $this->doo));
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
                        - features/bootstrap/config/**/*
        """
        And a config file "features/bootstrap/config/qux/services.php" containing:
        """
        <?php

        use Symfony\Component\DependencyInjection\Definition;
        use Symfony\Component\DependencyInjection\Parameter;

        $container->setParameter('qux', 'qux value');
        """
        And a config file "features/bootstrap/config/doo/services.xml" containing:
        """
        <container xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://symfony.com/schema/dic/services">
            <parameters>
                <parameter key="doo">doo value</parameter>
            </parameters>
        </container>
        """
        And a config file "features/bootstrap/config/services.yml" containing:
        """
        parameters:
            foobar: "shit happens"

        services:
            feature_context:
                class: FeatureContext
                arguments:
                    - "%foobar%"
                    - "%qux%"
                    - "%doo%"
                tags:
                    - { name: "fob.context_service" }
        """

    Scenario: Using registered context service
        Given a feature file "features/registering_context_service.feature" containing:
        """
        Feature: Registering context service

            Scenario:
                Given parameters were injected to the context
                Then they should contain "shit happens", "qux value", and "doo value"
        """
        When I run Behat
        Then it should pass

    Scenario: New context service instance is used for each scenario
        Given a feature file "features/registering_context_service.feature" containing:
        """
        Feature: Registering context service

            Scenario:
                Given parameters were injected to the context
                When I change them to "shit does not happen", "ooops", and "foobarquxdoo"
                Then they should contain "shit does not happen", "ooops", and "foobarquxdoo"

            Scenario:
                Given parameters were injected to the context
                Then they should contain "shit happens", "qux value", and "doo value"
        """
        When I run Behat
        Then it should pass
