# Context Service Extension [![License](https://img.shields.io/packagist/l/friends-of-behat/context-service-extension.svg)](https://packagist.org/packages/friends-of-behat/context-service-extension) [![Version](https://img.shields.io/packagist/v/friends-of-behat/context-service-extension.svg)](https://packagist.org/packages/friends-of-behat/context-service-extension) [![Build status on Linux](https://img.shields.io/travis/FriendsOfBehat/ContextServiceExtension/master.svg)](http://travis-ci.org/FriendsOfBehat/ContextServiceExtension) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/FriendsOfBehat/ContextServiceExtension.svg)](https://scrutinizer-ci.com/g/FriendsOfBehat/ContextServiceExtension/)

Allows to declare and use contexts services in scenario scoped container.

## Usage

1. Install it:
    
    ```bash
    $ composer require friends-of-behat/context-service-extension --dev
    ```

2. Enable and configure context service extension in your Behat configuration:
    
    ```yaml
    # behat.yml
    default:
        # ...
        extensions:
            FriendsOfBehat\ContextServiceExtension:
               imports:
                   - "features/bootstrap/config/services.xml"
                   - "features/bootstrap/config/services.yml"
                   - "features/bootstrap/config/services.php"   
    ```
    
3. Inside one of those files passed to configuration above, create a service tagged with `fob.context_service`.

    ```xml
    <!-- features/bootstrap/config/services.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <container xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://symfony.com/schema/dic/services">
        <services>
            <service id="acme.my_context" class="Acme\MyContext">
                <tag name="fob.context_service" />
            </service>
        </services>
    </container>
    ```
    
    ```yaml
    # features/bootstrap/config/services.yml
    services:
        acme.my_context:
            class: Acme\MyContext
            tags:
                - { name: fob.context_service }
    ```
    
    ```php
    // features/bootstrap/config/services.php
    use Symfony\Component\DependencyInjection\Definition;
    
    $definition = new Definition(\Acme\MyContext::class);
    $definition->addTag('fob.context_service');
    $container->setDefinition('acme.my_context', $definition);
    ```

4. Configure your suite to use `acme.my_context` context service (note **contexts_services** key instead of **contexts**):

    ```yaml
    # behat.yml
    default:
        # ...
           suites:
               default:
                   contexts_services:
                       - acme.my_context
    ```

5. Have fun with your contexts defined in DI container as services! :tada:
