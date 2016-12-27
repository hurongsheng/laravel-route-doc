# laravel-route-doc  --For Laravel 5.*

    create api doc based on route.php & controller's document.
    

### Install

Require this package with composer using the following command:

```bash
composer require hurongsheng/laravel-route-doc
```

### Usage

```php
add hurongsheng\LaravelRouteDoc\RouteDocServiceProvider::class,in app.php
php artisan vendor:publish --provider="hurongsheng\LaravelRouteDoc\RouteDocServiceProvider"
php artisan migrate
```

```php
declare SomeController extend hurongsheng\LaravelRouteDoc\Controllers\RouteDocController;
or declare Route::controller() for hurongsheng\LaravelRouteDoc\Controllers\RouteDocController;
```

```php
     change config in route_doc.php as your wish
     RouteDocController@getList return default view of doc
```

### Rules in controller document

	/**
     * @description   function description
     * @param Request $request
     * @param         $id
     * @request       $name
     * @request       $something
     * @return SomeClass
     * @author your name
     */
    
### Helper

#### add @description/@request document when in phpstorm 

    http://jingyan.baidu.com/article/48b558e35b81c27f38c09ab7.html
    
  
