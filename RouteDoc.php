<?php
/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/12/9
 * Time: 11:50
 */

namespace hurongsheng\LaravelRouteDoc;

use hurongsheng\LaravelRouteDoc\Lib\analyse_code;
use Route;
use Closure;
use  \hurongsheng\LaravelRouteDoc\Models\RouteDocModel;
use App;

Class RouteDoc
{
    protected $routes = [];

    public function __construct()
    {

    }

    public static function all()
    {
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)
            ->where('env', App::environment())
            ->orderBy('domain')->orderBy('uri')->orderBy('method')->get();
    }

    public static function getWorking()
    {
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)
            ->where('env', App::environment())->get();
    }

    public static function whereRequestDomain($where)
    {
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)
            ->where('env', App::environment())->where($where)
            ->orderBy('domain')->orderBy('uri')->orderBy('method')->get();
    }

    public static function whereRequestUpdated($where)
    {
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)
            ->where('env', App::environment())
            ->where($where)->orderBy('updated_at', 'desc')->get();
    }

    public static function btnList($key, $where)
    {
        if (key_exists($key, $where)) {
            unset($where[$key]);
        }
        return RouteDocModel::where('state', RouteDocModel::STATE_WORK)
            ->where('env', App::environment())->where($where)
            ->orderBy($key)->select([$key])->distinct()->get()->pluck($key)->toArray();
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

    public static function handleModel(RouteDocModel $model)
    {
        $params = $doc = [];
        if ($model->controller) {
            list($controller, $method) = explode("@", $model->controller);
            $analyse = new analyse_code($controller);
            try {
                $doc = $analyse->getFunctionDocument($method, $model->uri, $fixed_uri);
                $model->uri = $fixed_uri;
                $model->description = $doc['description'] ? : '';
                $model->author = $doc['author'] ? : '';
            } catch (\Exception $e) {
                $model->state = RouteDocModel::STATE_DELETE;
            }
        }
        if (self::matchUri($model, $uris)) {
            foreach ($uris[0] as $uri) {
                $uri_1 = str_replace("?", "", $uri);
                $desc = ($uri == $uri_1) ? '' : "(optional)";
                $params[$uri_1] = $doc['params'][$uri_1] ? : $desc;
                if ($model->where && $model->where[$uri_1]) {
                    $params[$uri_1] = $params[$uri_1] . '/' . $model->where[$uri_1] . '/';
                }
            }
        };
        $model->params = array_merge($doc['params'] ? : [], $params);
        $model->test_data = $model->test_data ? : [];
        $model->save();
        return $model;
    }

    public static function matchUri(RouteDocModel $model, &$uri_params = [])
    {
        return preg_match_all('/(?<=[{])[\S]+?(?=[}])/u', $model->uri, $uri_params);
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
            $model->env = App::environment();
            $model->save();
            $ids[] = $model->id;
        }
        if ($ids) {
            RouteDocModel::clearExcept($ids, App::environment());
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
            list($controller_name, $function) = explode("@", $doc['uses']);
            $controller_name = explode('\\', $controller_name);
            $doc['controller_name'] = array_pop($controller_name) ? : '';
            $doc['function'] = $function ? : '';

            $doc['controller'] = $action['controller'] ? : "";
            if (config('route_doc.hidden.missingMethod', 0) && preg_match('/@missingMethod/u', $doc['controller'])) {
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