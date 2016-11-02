Feature: Referencing cross containers services
    In order to allow my contexts services to be actually useful
    As a Behat Developer
    I want to be able to inject services from Behat container

    Background:
        Given a context file "features/bootstrap/FeatureContext.php" containing:
        """
        <?php

        use Behat\Behat\Context\Context;
        use Symfony\Component\DependencyInjection\ContainerInterface;

        class FeatureContext implements Context
        {
            private $container;

            public function __construct(ContainerInterface $container = null)
            {
                $this->container = $container;
            }

            /**
             * @Then Behat container should be injected to the context
             */
            public function behatContainerShouldBeInjectedToTheContext()
            {
                if (null === $this->container) {
                    throw new \DomainException('Nothing was injected!');
                }

                if (!$this->container->hasParameter('paths.base')) {
                    throw new \DomainException('Injected container does not have "paths.base" parameter!');
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
                        - features/bootstrap/config/services.xml

                FriendsOfBehat\CrossContainerExtension: ~
        """
        And a config file "features/bootstrap/config/services.xml" containing:
        """
        <container xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://symfony.com/schema/dic/services">
            <services>
                <service id="feature_context" class="FeatureContext">
                    <argument type="service" id="__behat__.service_container" />
                    <tag name="fob.context_service" />
                </service>
            </services>
        </container>
        """

    Scenario: Referencing cross container services
        Given a feature file "features/referencing_cross_container_services.feature" containing:
        """
        Feature: Referencing cross container services

            Scenario:
                Then Behat container should be injected to the context
        """
        When I run Behat
        Then it should pass
