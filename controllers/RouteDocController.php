<?php

namespace hurongsheng\LaravelRouteDoc\Controllers;

use \App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use hurongsheng\LaravelRouteDoc\Lib\analyse_code;
use \hurongsheng\LaravelRouteDoc\RouteDoc;
use hurongsheng\lib\analyse_route;
use Illuminate\Http\Request;
use \hurongsheng\LaravelRouteDoc\Models\RouteDoc as RouteDocModel;

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
     * @request       $html html replace $key&$value
     * @author hurs
     */
    public function getParams(Request $request)
    {
        $id = $request->input('id');
        $tr = $request->input('html', "<tr><td class='param'>%s</td><td class='value'><input value='%s'/></td></tr>");
        $model = RouteDocModel::findOrFail($id);
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
        $html = "";
        foreach ($model->params as $param) {
            $key = $param['param'];
            $value = $model->test_data['body'][$param['param']] ? : '';
            $html = $html . sprintf($tr, $key, $value);
        }
        $source_code = $model->test_data['success_code'] ? implode(",", $model->test_data['success_code']) : 200;
        $html = $html . sprintf($tr, 'success_code', $source_code);
        $model->html = $html;
        return $model;
    }

    /**
     * @description
     * @param Request $request
     * @author hurs
     */
    public function postTest(Request $request)
    {
        $id = $request->input('id');
        $body = $request->input('body', []);
        $headers = $request->input('headers', []);
        $success_code = $request->input('success_code', '');
        $model = RouteDocModel::findOrFail($id);
        $test_data = $model->test_data ? : [];
        $test_data['body'] = $body;
        $test_data['headers'] = $headers;
        $test_data['success_code'] = explode(",", $success_code) ? : [];
        $model->test_data = $test_data;
        $model->save();
        $this->sendRequest($request, $model);
    }

    public function getTest()
    {
        try {
            $client = new Client();
            $response = $client->get('http://app.ddd.com/route/params', [
                'headers' => [],
                'json' => ['id' => 63]
            ]);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return $e->getResponse()->getBody()->getContents();
        }

    }

    protected function sendRequest(Request $request, RouteDocModel $model)
    {
        $method = $model->method;
        if (!$model->domain) {
            $url = $request->getHost() . '/' . trim($model->uri, '/ ');
        } else {
            $url = implode('/', [trim($model->domain, '/ '), trim($model->uri, '/ ')]);
        }
        if (!preg_match('/http/', $url)) {
            $url = $request->getScheme() . '://' . $url;
        }
        $body = $model->test_data['body'] ? : [];
        $headers = $model->test_data['headers'] ? : [];
        $success_code = $model->test_data['success_code'] ? : [];
        try {
            $client = new Client();
            $response = $client->$method($url, [
                'headers' => $headers,
                'json' => $body
            ]);
            $code = $response->getStatusCode();
            $model->last_test = in_array($code, $success_code) ? 1 : 0;
            $model->save();
        } catch (\Exception $e) {
            $code = $e->getCode();
            $model->last_test = in_array($code, $success_code) ? 1 : 0;
            $model->save();
            throw new \Exception($e->getResponse()->getBody()->getContents(), $code);
        }
    }

    public function anyRefresh()
    {
        $doc = new RouteDoc();
        $doc->refresh();
    }
}