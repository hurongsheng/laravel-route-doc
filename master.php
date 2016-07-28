<?php

namespace hurongsheng;

class master
{
    protected $config = [];

    public function __construct()
    {
        $this->config = require_once("config.php");
        $route = base_path($this->config['route_file']);
        $route = file_get_contents($route);
        dump($route);
    }
}