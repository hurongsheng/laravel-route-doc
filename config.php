<?php

return [
    /*
     * table name in database
     */
    'table_name' => 'route_doc',
    'uri' => [
        'params' => 'route/params',
        'test' => 'route/test',
    ],

    /*
     * methods in list will be created in doc
     */
    'route_methods' => [
        'GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'PATCH'
    ],
    'view_show' => [
        'domain' => 'domain',
        'uri' => 'uri',
        'method' => 'method',
        'where' => 'where',
        'params' => 'params',
        'description' => 'description',
        'author' => 'author',
        'updated_at' => 'update time',
        'last_test' => 'last test',
    ],
    /*
     * hidden something. need refresh
     */
    'hidden' => [
        '_missing' => 1,
        'missingMethod' => 1,
    ]

];