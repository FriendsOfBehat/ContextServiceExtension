Feature: Registering contexts services

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
                        - features/bootstrap/config/services.xml
        """
        And a config file "features/bootstrap/config/services.xml" containing:
        """
        <container xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://symfony.com/schema/dic/services">
            <parameters>
                <parameter key="foobar">shit happens</parameter>
            </parameters>

            <services>
                <service id="feature_context" class="FeatureContext">
                    <argument>%foobar%</argument>
                    <tag name="fob.context_service" />
                </service>
            </services>
        </container>
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
