<?php

namespace hurongsheng\LaravelRouteDoc\Models;

use \Eloquent;

class RouteDoc extends Eloquent
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
        return \DB::table(with(new static())->table)->whereNotIn('id', $ids)->update(['state' => static::STATE_DELETE]);
    }
}
