# WebHookHandler
Log-Hander that POSTs a log-request using HTTPlug

## Installation

```bash
composer require org_Heigl/webhookhandler
```

## Usage

1. Create the handler

```php
    $uriFactory = UriFactoryDiscovery::find();
    $uri = $uriFactory->createUri('http://example.com/');
    
    $handler = new WebHookHandler($uri);
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

