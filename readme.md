# laravel-route-doc  --For Laravel 5.*

    create api doc based on route.php & controller's document.
    

### Install

Require this package with composer using the following command:

```bash
composer require hurongsheng/laravel-route-doc
```

### Usage

```php
add hurongsheng\LaravelRouteDoc\RouteDocServiceProvider::class into config/app.php--providers
php artisan vendor:publish --provider="hurongsheng\LaravelRouteDoc\RouteDocServiceProvider"
php artisan migrate
```

```php
declare SomeController extend hurongsheng\LaravelRouteDoc\Controllers\RouteDocController;
and declare Route::controller() SomeController;
hurongsheng\LaravelRouteDoc\Controllers\RouteDocController extend App\Http\Controllers\Controller
```

```php

     config:config/route_doc.php
     RouteDocController@getManage return default view of doc
     RouteDocController@getList return default view of doc
     
     visit manage and click update route&update doc then wait reload
     
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
     
     @request==@param
    
### Helper

#### add @description @request document when in phpstorm 

    http://jingyan.baidu.com/article/48b558e35b81c27f38c09ab7.html
    
  
