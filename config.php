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
    'view_show' => [
        'domain' => 'domain',
        'uri' => 'uri',
        'method' => 'method',
//        'as' => 'as',
//        'uses' => 'uses',
//        'controller' => 'controller',
//        'namespace' => 'namespace',
//        'prefix' => 'prefix',
//        'group' => 'group',
//        'group2' => 'group2',
//        'where' => 'where',
        'params' => 'params',
        'description' => 'description',
        'author' => 'author',
        'updated_at' => 'update time',
        'last_test' => 'test result',
//        'test_data' => 'test_data',
    ],
    /*
     * hidden something. need refresh
     */
    'hidden' => [
        '_missing' => 1,
        'missingMethod' => 1,
    ]

];