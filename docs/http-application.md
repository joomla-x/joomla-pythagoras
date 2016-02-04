# HTTP Middleware

There are several ways to define middleware for the HTTP processing.

## Class implementing `Joomla\HTTP\MiddlewareInterface`

```php
$app = new Application([
    new MyMiddleware($constructorParams),
], $container);
```

with `MyMiddleware` looking like

```php
<?php

namespace Vendor\HTTP\Middleware;

use Joomla\HTTP\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MyMiddleware implements MiddlewareInterface
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param callable          $next
     *
     * @return ResponseInterface
     */
    public function handle(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        // Do stuff before

        $response = $next($request, $response);
        
        // Do stuff after

        return $response;
    }
}
```
    
## Closure

```php
$app = new Application([
    function (
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        // Do stuff before

        $response = $next($request, $response);

        // Do stuff after

        return $response;
    },
], $container);
```

## Callable

```php
$app = new Application([
    [new MyMiddleware($constructorParams), 'callableHandler'],
    // or statically
    [MyMiddleware::class, 'staticHandler'],
], $container);
```

with `MyMiddleware::callableHandler` looking like

```php
<?php

namespace Vendor\HTTP\Middleware;

use Joomla\HTTP\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MyMiddleware
{
    // ...
    
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param callable          $next
     *
     * @return ResponseInterface
     */
    public function callableHandler(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        // Do stuff before
        
        $response = $next($request, $response);

        // Do stuff after

        return $response;
    }
    
    // ...
}
```
