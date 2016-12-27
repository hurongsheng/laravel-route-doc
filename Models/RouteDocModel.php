<?php

namespace hurongsheng\LaravelRouteDoc\Models;

use \Eloquent;

class RouteDocModel extends Eloquent
{
    const STATE_DELETE = 0;
    const STATE_WORK = 1;
    protected $table = 'route_doc';
    protected $casts = ['where' => 'array', 'test_data' => 'array', 'params' => 'array'];
    protected $fillable = [
        'domain', 'uri', 'method'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('route_doc.table_name', $this->table);
        $this->connection = config('route_doc.table_connection', config('database.default'));
    }

    public static function getUnique($domain, $uri, $method)
    {
        return static::firstOrNew([
            'domain' => $domain,
            'uri' => $uri,
            'method' => $method
        ]);
    }

    public static function clearExcept($ids)
    {
        $model = with(new static());
        return \DB::connection($model->connection)->table($model->table)->whereNotIn('id', $ids)->update(['state' => static::STATE_DELETE]);
    }
}
