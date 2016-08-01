<?php
/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/7/27
 * Time: 17:56
 */

namespace hurongsheng\lib;

use ReflectionClass;

class analyse_code
{

    protected $reflection;
    protected $controller;
    protected $hidden_param;
    protected $hidden_param_name = 'hidden_params';

    public function __construct($controller)
    {
        $this->controller = $controller;
        $config = require(dirname(dirname(__FILE__)) . "/config.php");
        $this->hidden_param = $config[$this->hidden_param_name];
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
     * @return string
     * @author hurs
     */
    public function getFunctionDocument($function, $url)
    {
        $method = $this->getReflection()->getMethod($function);
        return $this->getDocument($method, $url);
    }

    public function getDocument(\ReflectionMethod $method, $url)
    {
        $doc = $method->getDocComment();
        $doc = [
            'author' => $this->getAuthor($doc) ? : '',
            'description' => $this->getDescription($doc) ? : '',
            'params' => $this->getParams($doc, $url) ? : [],
        ];
        return $doc;
    }

    public function getParams($doc, $url)
    {
        $preg_url = "/(?<=[\{])[a-z\_]+(?=[\}])/";
        preg_match_all($preg_url, $url, $hidden_urls);
        $preg = "/(?<=@param |@request )[ \t\S]*/";
        $preg2 = "/(?<=[$])[ \t\S]*/";
        $preg3 = "/[\S]+/";
        preg_match_all($preg, $doc, $matches);
        foreach ($matches[0] as $key => &$match) {
            preg_match($preg2, $match, $m);
            preg_match_all($preg3, $m[0], $m);
            $match = ['param' => $m[0][0]];
            if (in_array($match['param'], array_merge($this->hidden_param, $hidden_urls[0] ? : []))) {
                unset($matches[0][$key]);
                continue;
            }
            unset($m[0][0]);
            $match['desc'] = implode(" ", $m[0]) ? : '';

        }
        return $matches[0];
    }

    public function getDescription($doc)
    {
        return $this->getBaseData($doc, 'description');
    }

    public function getAuthor($doc)
    {
        return $this->getBaseData($doc, 'author');
    }

    protected function getBaseData($doc, $key)
    {
        $preg = "/(?<=@$key )[ \t\S]*/";
        preg_match($preg, $doc, $match);
        return $match[0];
    }

}