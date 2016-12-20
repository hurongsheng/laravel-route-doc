<?php
/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/12/9
 * Time: 11:50
 */

namespace hurongsheng\LaravelRouteDoc;

use \Route;

Class RouteDoc
{
    protected $routes = [];

    public function __construct()
    {
    }

    public function refresh()
    {
        $routes = Route::getRoutes()->getRoutes();
        foreach ($routes as $route) {

        }
    }
}