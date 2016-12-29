<?php
/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/7/27
 * Time: 17:56
 */

namespace hurongsheng\lib;

use Exception;
use Illuminate\Routing\ControllerInspector;

class analyse_route
{
    protected $config = [];
    protected $config_name = 'controller_namespace';

    public function __construct()
    {
        $this->config = require(dirname(dirname(__FILE__)) . "/config.php");
        $this->config = $this->config[$this->config_name];
    }

    public function getRoute($route_body, &$item)
    {
        $method = $route_body['method'];
        switch ($route_body['type']) {
            case 'route':
                return $this->$method($route_body, $item);
            default:
                return $route_body;
        }
    }

    public function getPhp($controller)
    {
        $code = new analyse_code($controller);
        $code->getReflection();
        return $code;
    }

    /**
     * @param        $name
     * @param string $method
     * @return analyse_code
     * @throws Exception
     * @author hurs
     */
    public function getController($name, &$method = "")
    {
        list($controller, $method) = explode("@", $name);
        $controller = "\\" . trim($this->config . "\\" . $controller, "\\");
        try {
            return $this->getPhp($controller);
        } catch (Exception $e) {
        }
        throw new Exception('有未找到的controller,请核实:' . $name, 404);
    }

    protected function get($route_body, $item = "")
    {
        $code = $this->getController($route_body['controller'], $method);
        if (!$method) {
            return $route_body;
        }
        $function = $code->getFunctionDocument($method, $route_body['route']);
        return array_merge($route_body, $function);
    }

    protected function post($route_body, $item = "")
    {
        return $this->get($route_body);
    }

    protected function delete($route_body, $item = "")
    {
        return $this->get($route_body);
    }

    protected function put($route_body, $item = "")
    {
        return $this->get($route_body, $item = "");
    }

    protected function any($route_body, $item = "")
    {
        return $this->get($route_body);
    }

    protected function controller($route_body, &$item = "")
    {
        $controllerInspector = new ControllerInspector();
        $controller_rules = "/(?<=get|post|any|put|delete)[\S]+/u";
        $code = $this->getController($route_body['controller']);
        $methods = $code->getFunctions();
        foreach ($methods as $method) {
            if (preg_match($controller_rules, $method->name, $match) && $method->isPublic()) {
                if (strstr("\\" . $method->class, $this->config)) {
                    $route_body_tmp = $route_body;
                    $route_body_tmp['route'] = $controllerInspector->getPlainUri($method->name, $route_body_tmp['route']);
                    $route_body_tmp['method'] = $controllerInspector->getVerb($method->name);
                    $function = $code->getDocument($method, $route_body_tmp['route']);
                    $route_body_tmp = array_merge($route_body_tmp, $function);
                    $item[$route_body_tmp['route']][$route_body_tmp['method']] = $route_body_tmp;
                }
            }
        }
    }


    protected function resource($route_body)
    {
        return $route_body;
    }

    public function __call($name, $arguments)
    {
        return $arguments[0];
    }

}