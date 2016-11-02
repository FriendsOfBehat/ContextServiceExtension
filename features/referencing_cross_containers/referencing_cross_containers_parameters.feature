Feature: Referencing cross containers parameters
    In order to allow my contexts services to be actually useful
    As a Behat Developer
    I want to be able to inject parameters from Behat container

    Background:
        Given a context file "features/bootstrap/FeatureContext.php" containing:
        """
        <?php

        use Behat\Behat\Context\Context;

        class FeatureContext implements Context
        {
            private $basePaths;

            public function __construct($basePaths)
            {
                $this->basePaths = $basePaths;
            }

            /**
             * @Then base paths should be injected to the context
             */
            public function basePathsShouldBeInjectedToTheContext()
            {
                if (null === $this->basePaths) {
                    throw new \DomainException('No base paths were injected!');
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
                    <argument>%__behat__.paths.base%</argument>
                    <tag name="fob.context_service" />
                </service>
            </services>
        </container>
        """

    Scenario: Referencing cross container parameters
        Given a feature file "features/referencing_cross_container_parameters.feature" containing:
        """
        Feature: Referencing cross container parameters

            Scenario:
                Then base paths should be injected to the context
        """
        When I run Behat
        Then it should pass
