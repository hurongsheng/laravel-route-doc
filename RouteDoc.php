<?php
/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/12/9
 * Time: 11:50
 */

namespace hurongsheng\LaravelRouteDoc;

use Route;
use Closure;
use  \hurongsheng\LaravelRouteDoc\Models\RouteDoc as RouteDocModel;

Class RouteDoc
{
    protected $routes = [];

    public function __construct()
    {

    }

    public function all()
    {
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)->orderBy('domain')->orderBy('uri')->orderBy('method')->get();
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)->orderBy('domain')->orderBy('uri')->orderBy('method')->get();
    }

    public function refresh()
    {
        $routes = Route::getRoutes()->getRoutes();
        $method_need = config('route_doc.route_methods', []);
        $docs = [];
        foreach ($routes as $route) {
            $this->analyseRoute($route, $method_need, $docs);
        }
        $this->create($docs);
    }

    protected function create($docs)
    {
        $ids = [];
        foreach ($docs as $doc) {
            $model = RouteDocModel::getUnique($doc['domain'], $doc['uri'], $doc['method']);
            foreach ($doc as $key => $value) {
                $model->$key = $value;
            }
            $model->state = RouteDocModel::STATE_WORK;
            $model->save();
            $ids[] = $model->id;
        }
        if ($ids) {
            RouteDocModel::clearExcept($ids);
        }
    }

    protected function analyseRoute($route, $method_need, &$docs = [])
    {
        $methods = $route->getMethods();
        $action = $route->getAction();
        $wheres = RouteTransClass::getInstance($route)->getWheres() ? : [];
        $uri = $this->handleUri($route->getUri());
        $action['uri'] = $uri;
        foreach ($methods as $method) {
            if (!in_array($method, $method_need)) {
                continue;
            }
            $doc['domain'] = $action['domain'] ? : '';
            $doc['uri'] = $uri;
            $doc['method'] = $method;
            $doc['as'] = $action['as'] ? : '';
            if ($action['uses'] instanceof Closure) {
                $doc['uses'] = 'Closure';
            } elseif (is_string($action['uses'])) {
                $doc['uses'] = $action['uses'];
            } else {
                $doc['uses'] = 'unknow';
            }
            $doc['controller'] = $action['controller'] ? : "";
            if (config('route_doc.hidden.missingMethod', 0) && preg_match('/@missingMethod/', $doc['controller'])) {
                continue;
            }
            $doc['namespace'] = $action['namespace'] ? : "";
            $doc['prefix'] = $action['prefix'] ? : '';
            $doc['where'] = $wheres;
            $docs[] = $doc;
        }
    }

    protected function handleUri($uri)
    {
        $uri = str_replace("/{one?}/{two?}/{three?}/{four?}/{five?}", "", $uri);
        if (config('route_doc.hidden._missing', 0)) {
            $uri = str_replace("/{_missing}", "", $uri);
        }
        return $uri;
    }
}