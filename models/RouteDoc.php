<?php

namespace hurongsheng\LaravelRouteDoc\models;

use \Eloquent;

class RouteDoc extends Eloquent
{
    protected $table = 'route_doc';
    protected $fillable = ['where', 'test_data'];

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
        $this->table = config('route_doc.table_name', $this->table);
    }

    public function getMethod()
    {

    }
}
