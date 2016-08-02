<?php

return [
    'route_file' => config('route_doc.route_file', 'app/Http/routes.php'),
    'controller_namespace' => config('route_doc.controller_namespace', '\App\Http\Controllers'),
    'ignore_params' => config('route_doc.ignore_params', ['request']),
];