<?php
/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/7/27
 * Time: 17:56
 */

namespace hurongsheng\LaravelRouteDoc\Lib;

use ReflectionClass;

class analyse_code
{

    protected $reflection;
    protected $controller;
    protected $hidden_param;
    protected $hidden_param_name = 'ignore_params';

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->hidden_param = config('route_doc.ignore_params');
    }

    public function getReflection()
    {
        $this->reflection = $this->reflection ? : new ReflectionClass($this->controller);
        return $this->reflection;
    }

    public function getFunction($function)
    {
        return $this->getReflection()->getMethod($function);
    }

    public function getFunctions()
    {
        return $this->getReflection()->getMethods();
    }

    /**
     * @param $function
     * @param $url
     * @param $fixed_uri
     * @return string
     * @author hurs
     */
    public function getFunctionDocument($function, $url, &$fixed_uri)
    {
        $parameters = [];
        $preg = "/(?<=[\{])[\S]+?(?=[?\}]+)/u";
        $method = $this->getReflection()->getMethod($function);
        foreach ($method->getParameters() as $position => $parameter) {
            if (!$parameter->getClass()) {
                $parameters[] = $parameter->getName();
            }
        }
        $fixed_uri = explode("/", $url);
        $i = 0;
        foreach ($fixed_uri as &$u) {
            if (preg_match($preg, $u, $match)) {
                $u = $parameters[$i] ? preg_replace($preg, $parameters[$i], $u) : $u;
                $i++;
            }
        }
        $fixed_uri = implode("/", $fixed_uri);
        return $this->getDocument($method, $fixed_uri);
    }

    public function getDocument(\ReflectionMethod $method, $url)
    {
        $doc = $method->getDocComment();
        $params = $this->getParams($doc, $url);
        $doc = [
            'author'      => $this->getAuthor($doc) ?: '',
            'description' => $this->getDescription($doc) ?: '',
            'return' => $this->getReturn($doc) ?: '',
            'params'      => [],
            'types'       => [],
        ];
        $doc = array_merge($doc, $params);
        return $doc;
    }

    public function getParams($doc, $url)
    {
//        $preg_url = "/(?<=[\{])[a-z\_]+(?=[\}])/u";
//        preg_match_all($preg_url, $url, $hidden_urls);
        $preg_line = "/(?<=@param |@request )[ \t\S]*/u";
        $preg_param = "/(?<=[$])[ \t\S]*/u";
        $preg_param_type = "/[ \t\S]*(?=[ ][$])/u";
        $preg_param_desc = "/[\S]+/u";
        preg_match_all($preg_line, $doc, $matches);
        $params = ['params' => '', 'types' => ''];
        foreach ($matches[0] as $key => &$match) {
            preg_match($preg_param, $match, $m);
            preg_match($preg_param_type, $match, $type);
            $type = trim($type[0]);
            preg_match_all($preg_param_desc, $m[0], $m);
            $k = $m[0][0];
            if (!in_array($k, $this->hidden_param)) {
                unset($m[0][0]);
                $params['params'][$k] = implode(" ", $m[0]) ? : '';
                $params['types'][$k] = $type;
            }
        }
        return $params;
    }

    public function getDescription($doc)
    {
        return $this->getBaseData($doc, 'description');
    }

    public function getAuthor($doc)
    {
        return $this->getBaseData($doc, 'author');
    }
    public function getReturn($doc)
    {
        return $this->getBaseData($doc, 'return');
    }

    protected function getBaseData($doc, $key)
    {
        $preg = "/(?<=@$key )[ \t\S]*/u";
        preg_match($preg, $doc, $match);
        return $match[0];
    }

}