# WebHookHandler
Log-Hander that POSTs a log-request using HTTPlug

## Installation

```bash
composer require org_Heigl/webhookhandler
```

## Usage

1. Create the handler

```php    
    $uriFactory = \Http\Discovery\UriFactoryDiscovery::find();
    $uri = $uriFactory->createUri('http://example.com/');
        
    $handler = new WebHookHandler(
        $uri,
        Logger::DEBUG,
        \Http\Discovery\HttpAsyncClientDiscovery::find(),
        Http\Discovery\MessageFactoryDiscovery::find()
    );
    
    $handler->setFrom('WhateverYouWant');
```

2. Add the handler to the logger as you would with any other handler:

```php
    $logger = new Logger('example');
    $logger->pushHandler($handler);
```

3. Log as you are used to:

```php
    $logger->log('Whatever you want to say');
```

The log-message will be send via a HTTP-POST to the provided URI (in this 
example to ```http://example.com/``).

The post-body will be the following json_encoded array:

    [
        [message] => The message of the log-entry
        [from] => 'WhateverYouWant'
        [context] => []
        [level] => the set log-level
        [level_name] => the name of the set log-level
        [channel] => Whatever you set as channel name for the logger
        [datetime] => DateTime-Object
        [extra] => []
        [formatted] => The formatted message
     ]

That mainly is the array that monolog passes to the handlers…