# laravel-route-doc  --For Laravel 5.*

    create api doc based on route.php & controller's document. supported simple route rules
    

### Install

Require this package with composer using the following command:

```bash
composer require hurongsheng/laravel-route-doc
```

### Usage

```php
add hurongsheng\RouteDocServiceProvider::class in app.php
php artisan vendor:publish --provider="hurongsheng/LaravelRouteDoc/RouteDocServiceProvider"
```

```php
$route=new route();
$data=$route->getDoc();
$view=$route->getDefaultView($view_title);
```


### Rules in route.php

start with
	
	//## start

end with

	//## end
	
group by

	//### something
	
routes

* //#### url method params descriptions
* //#### url method descrption
* Route::get()
* Route::post()
* Route::delete()	
* Route::put()
* Route::any()
* ...
* Route::controller()
* Route::resource()


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

### Default

* add file [route_doc.blade.php] in view_path to overload default view 
* add config [route_doc.php] in config_path to overload default config 
* config('route_doc.route_file') as your route.php file path
* config('route_doc.controller_namespace') as your controller file  namespace
* config('route_doc.ignore_params') 


### Helper

#### add @description/@request document when in phpstorm 

    http://jingyan.baidu.com/article/48b558e35b81c27f38c09ab7.html
    
    
\hurongsheng\LaravelRouteDoc\RouteDocServiceProvider::class
composer dump-autoload
php artisan optimize
php artisan vendor:publish
