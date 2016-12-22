<?php

namespace hurongsheng\LaravelRouteDoc;

/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/12/22
 * Time: 12:45
 */
class RouteTransClass extends \Illuminate\Routing\Route
{

    public function getWheres()
    {
        $wheres = $this->wheres;
        if (key_exists('_missing', $wheres) && config('route_doc.hidden._missing',0)) {
            unset($wheres['_missing']);
        }
        return $wheres;
    }

    public static function getInstance(\Illuminate\Routing\Route $route)
    {
        $new = new static($route->getMethods(), $route->getUri(), $route->getAction());
        foreach ($route as $key => $value) {
            $new->$key = $value;
        }
        return $new;
    }
}