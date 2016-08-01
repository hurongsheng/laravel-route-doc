<?php

namespace hurongsheng;

use hurongsheng\lib\analyse_route;

class master
{
    protected $config = [];
    protected $config_name = 'route_file';
    protected $api_doc = [];

    public function __construct()
    {
        $this->config = require_once("config.php");
        $route = base_path($this->config[$this->config_name]);
        $body = file_get_contents($route);
        $body = $this->getBody($body);
        $h3 = '';
        $api_doc = [];
        foreach ($body as $doc) {
            if (!trim($doc)) {
                continue;
            }
            $match = $this->getH3($doc);
            if ($match) {
                $h3 = $match;
            } elseif ($h3) {
                $res = $this->getH4($doc) ? : $this->getRoute($doc);
                $api_doc[$h3][$res['route']][$res['method']] = $res;
            }
        }
        $route = new analyse_route();
        foreach ($api_doc as &$items) {
            foreach ($items as &$item) {
                foreach ($item as $method => &$data) {
                    $data = $route->getRoute($data, $items);
                    if (!$data) {
                        unset($item[$method]);
                    }
                }
            }
        }
        $this->api_doc = $api_doc;
    }

    public function getDoc()
    {
        return $this->api_doc;
    }

    public function getDefaultView($title = "api doc")
    {
        $view_path = realpath(base_path("vendor/hurongsheng/laravel-route-doc/lib"));
        \View::addLocation($view_path);
        return view('view', ['api_doc' => $this->getDoc(), 'title' => $title]);
    }

    protected function getBody($text)
    {
        $preg = "/(?<=\/\/## start)[\s\S]*(?=\/\/## end)/";
        preg_match($preg, $text, $match);
        $body = explode("\n", trim($match[0]));
        return $body;
    }

    protected function getH3($text)
    {
        $preg = ' / (?<=\/\/### )[\s\S]*/';
        preg_match($preg, $text, $match);
        return trim($match[0]);
    }

    protected function getH4($text)
    {
        $preg = "/\/\/####[\s\S]*/";
        $preg2 = "/\s*\S*/";
        $api = [];
        if (preg_match($preg, $text, $match)) {
            $api['type'] = 'h4';
            preg_match_all($preg2, trim($match[0]), $list);
            $list = $list[0];
            unset($list[0]);
            $api['route'] = trim(array_pull($list, 1));
            $api['method'] = trim(strtolower(array_pull($list, 2)));
            if ($list[4]) {
                $api['params'] = trim(array_pull($list, 3));
                $api['description'] = trim(implode(" ", $list));
            } else {
                $api['params'] = '';
                $api['description'] = trim(array_pull($list, 3));
            }
        }
        return $api;
    }

    protected function getRoute($text)
    {
        $preg = "/(?<=Route)[\s\S]*/";
        $preg2 = "/(?<=::)\S*(?=\()|(?<=\')\S*(?=\')/";
        $api = [];
        if (preg_match($preg, $text, $match)) {
            $api['type'] = 'h4';
            preg_match_all($preg2, $match[0], $list);
            $api = [
                'type' => 'route',
                'method' => $list[0][0],
                'route' => $list[0][1],
                'controller' => $list[0][2],
            ];
        }
        return $api;
    }
}