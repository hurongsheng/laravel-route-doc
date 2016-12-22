<?php

namespace hurongsheng\LaravelRouteDoc\Controllers;

use \App\Http\Controllers\Controller;
use hurongsheng\LaravelRouteDoc\Lib\analyse_code;
use \hurongsheng\LaravelRouteDoc\RouteDoc;
use hurongsheng\lib\analyse_route;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Client;

/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/12/9
 * Time: 11:29
 */
class  RouteDocController extends Controller
{
    public function getList()
    {
        $doc = new RouteDoc();
        $docs = $doc->all()->toArray();
        $keys = $docs[0] ? array_keys($docs[0]) : [];
        \App::make('RouteDoc');
        $show = config('route_doc.view_show');
        return view('RouteDoc::list', [
            'docs' => $docs,
            'keys' => $keys,
            'show' => $show,
        ]);
    }

    /**
     * @description get route info from code
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @request       $id route_doc_id
     * @author hurs
     */
    public function getParams(Request $request)
    {
        $id = $request->input('id');
        $model = \hurongsheng\LaravelRouteDoc\Models\RouteDoc::findOrFail($id);
        if ($model->controller) {
            list($controller, $method) = explode("@", $model->controller);
            $analyse = new analyse_code($controller);
            $doc = $analyse->getFunctionDocument($method, $model->uri);
            $model->description = $doc['description'] ? : '';
            $model->author = $doc['author'] ? : '';
            $model->params = $doc['params'] ? : [];
            $model->test_data = $model->test_data ? : [];
            $model->save();
        } else {
            $model->params = [];
            $model->test_data = $model->test_data ? : [];
            $model->save();
        }
        return $model;
    }

    /**
     * @description
     * @author hurs
     */
    public function postTest(Request $request)
    {
        $id = $request->input('id');
        $data = $request->input('data', []);
        $success_code = $request->input('success_code', []);
        $model = \hurongsheng\LaravelRouteDoc\Models\RouteDoc::findOrFail($id);
        $model->test_data['data'] = $data;
        $model->test_data['success_code'] = $success_code;
        $model->save();
    }

    public function anyRefresh()
    {
        $doc = new RouteDoc();
        $doc->refresh();
    }
}