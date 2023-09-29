# auth-middleware
A Laravel/Lumen middleware to authorize requests using CIAM ForgeRock.

## Installation
```
$ composer require ari-salt/auth-middleware
```

## Usage
Add these environments to your app. `CIAM_AUDIENCES` and `CIAM_IIS` are arrays of strings separated by commas.
```
CIAM_ALGORITHM=""
CIAM_AUDIENCES=""
CIAM_CACHE_EXPIRATION_HOURS=24
CIAM_CLIENT_ID=""
CIAM_HOST=""
CIAM_HTTP_TIMEOUT=3
CIAM_IIS=""
PEM_PUBLIC_KEY=""
```
Register middlewares to the routes.
```php
use AriSALT\AuthMiddleware\AuthOfflineMiddleware;
use AriSALT\AuthMiddleware\AuthOnlineMiddleware;

$app->routeMiddleware([
    'auth_offline' => AuthOfflineMiddleware::class,
    'auth_online' => AuthOnlineMiddleware::class
]);
```
Apply them to the routes.
```php
$router->get('/test', [
    'middleware' => [
        'auth_offline:memberForgeRock,VERIFY_TOKEN,forge-rock',
        // 'member:memberForgeRock,memberPimcore,VERIFY_TOKEN,forge-rock',
    ],
    'uses' => 'ExampleController@index'
]);
```
Then, you can use it on your handlers.
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index(Request $request)
    {
        var_dump($request->get('memberForgeRock'));
    }
}

```