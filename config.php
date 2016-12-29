<?php

return [
    /*
     * table name in database
     */
    'table_name' => 'route_doc',
    'table_connection' => '',

    /*
     * uri import ignore $request
     * default ignore params which in uri
     */
    'ignore_params' => [
        'request',
    ],
    /*
     * methods in list will be created in doc
     */
    'route_methods' => [
        'GET', 'POST', 'PUT', 'DELETE',
//        'HEAD', 'PATCH'
    ],
    /*
     * test methods' default headers
     */
    'default_headers' => [
        'Accept' => 'application/json',
    ],

    /*
     * show select & btn
     */
    'btn_list' => [
        'list' => ['domain', 'method', 'controller_name', 'function', 'author'],
        'manage' => [
            'domain', 'controller_name', 'function',
            ['method' => 'post', 'uri' => 'refresh', 'name' => 'update from route'],
            ['method' => 'get', 'uri' => 'params-all', 'name' => 'update from doc'],
        ],
    ],

    'view_show' => [
        'list' => [
            'domain' => 'domain',
            'uri' => 'uri',
            'method' => 'method',
            'params' => 'params',
            'description' => 'description',
            'author' => 'author',
            'controller_name' => 'controller_name',
            'updated_at' => 'update time ↓',
        ],
        'manage' => [
            'domain' => 'domain',
            'uri' => 'uri',
            'method' => 'method',
            'uses' => 'uses',
            'description' => 'description',
            'author' => 'author',
            'last_test' => 'test result',
            'updated_at' => 'update time',
        ],
    ],
    /*
     * hidden something. need refresh
     */
    'hidden' => [
        '_missing' => 1,
        'missingMethod' => 1,
    ]

];