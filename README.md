# Context Service Extension [![License](https://img.shields.io/packagist/l/friends-of-behat/context-service-extension.svg)](https://packagist.org/packages/friends-of-behat/context-service-extension) [![Version](https://img.shields.io/packagist/v/friends-of-behat/context-service-extension.svg)](https://packagist.org/packages/friends-of-behat/context-service-extension) [![Build status on Linux](https://img.shields.io/travis/FriendsOfBehat/ContextServiceExtension/master.svg)](http://travis-ci.org/FriendsOfBehat/ContextServiceExtension) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/FriendsOfBehat/ContextServiceExtension.svg)](https://scrutinizer-ci.com/g/FriendsOfBehat/ContextServiceExtension/)

Allows to declare and use contexts services in scenario scoped container.

## Usage

1. Install it:
    
    ```bash
    $ composer require friends-of-behat/context-service-extension --dev
    ```

2. Enable and configure context service extension in your Behat configuration:
    
    ```yaml
    default:
        # ...
        extensions:
            FriendsOfBehat\ContextServiceExtension: ~ # TODO!
    ```

3. Every suite you create will have those settings as the default ones.
