<?php

namespace hurongsheng\LaravelRouteDoc\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use hurongsheng\LaravelRouteDoc\RouteDoc;
use Illuminate\Http\Request;
use hurongsheng\LaravelRouteDoc\Models\RouteDocModel;
use Closure;

/**
 * Created by PhpStorm.
 * User: rongshenghu
 * Date: 16/12/9
 * Time: 11:29
 */
class  RouteDocController extends Controller
{

    /**
     * @description list view
     * @param Request $request
     * @return mixed
     * @author hurs
     */
    public function getList(Request $request)
    {
        return $this->view($request, 'list', function ($where) {
            return RouteDoc::whereRequestUpdated($where)->toArray();
        });
    }
    /**
     * @description list view
     * @param Int $id
     * @param Request $request
     * @return mixed
     * @author hurs
     */
    public function getDetail(Request $request, $id)
    {
        return view("RouteDoc::detail", [
            'btn_list' => [],
            'doc' => RouteDoc::find($id),
            'show' => config("route_doc.view_show.detail"),
        ]);
    }

    /**
     * @description manage view
     * @param Request $request
     * @return mixed
     * @author hurs
     */
    public function getManage(Request $request)
    {
        return $this->view($request, 'manage', function ($where) {
            return RouteDoc::whereRequestDomain($where)->toArray();
        });
    }

    protected function view(Request $request, $name, Closure $function)
    {
        $where = [];
        $btn_list = config("route_doc.btn_list.$name", []);
        foreach ($btn_list as &$btn) {
            if (is_string($btn) && $request->exists($btn)) {
                $where[$btn] = $request->input($btn);
            }
        }
        foreach ($btn_list as &$btn) {
            if (is_string($btn)) {
                $btn = ['data' => RouteDoc::btnList($btn, $where), 'type' => 'select', 'key' => $btn];
            } else {
                $btn['type'] = 'request';
            }
        }
        $docs = $function($where);
        \App::make('RouteDoc');
        return view("RouteDoc::$name", [
            'docs' => $docs,
            'keys' => $docs[0] ? array_keys($docs[0]) : [],
            'show' => config("route_doc.view_show.$name"),
            'btn_list' => $btn_list,
            'where' => $where,
        ]);
    }


    /**
     * @description update from doc
     * @param Request $request
     * @author hurs
     */
    public function getParamsAll(Request $request)
    {
        $models = RouteDoc::getWorking();
        foreach ($models as $model) {
            try {
                RouteDoc::handleModel($model);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @description get route info from code
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @request       $id route_doc_id
     * @request       $html html replace {$key} & {$value} & {$desc}
     * @author hurs
     */
    public function getParams(Request $request)
    {
        $id = $request->input('id');
        $tr = $request->input('html', "");
        $model = RouteDocModel::findOrFail($id);
        $model = RouteDoc::handleModel($model);
        return $this->formatHtml($model, $tr);
    }

    protected function formatHtml(RouteDocModel $model, $tr)
    {
        $html = "";
        foreach ($model->params as $key => $desc) {
            $value = $model->test_data['body'][$key] ? : '';
            $html = $html . $this->replaceHtml($tr, $key, $value, $desc);
        }
        $success_code = $model->test_data['success_code'] ? implode(",", $model->test_data['success_code']) : 200;
        $model->html = $html . $this->replaceHtml($tr, 'success_code', $success_code, 'success status code like 200,302,404');
        return $model;
    }

    protected function replaceHtml($tr, $key, $value = "", $desc = "")
    {
        $tr = str_replace('{$key}', $key, $tr);
        $tr = str_replace('{$value}', $value, $tr);
        $tr = str_replace('{$desc}', $desc, $tr);
        return $tr;
    }

    /**
     * @description test route
     * @param Request $request
     * @request  $id route_doc_id
     * @request  $body request body
     * @request  $headers request headers=[]
     * @author hurs
     */
    public function postTestRoute(Request $request)
    {
        $id = $request->input('id');
        $body = $request->input('body', []);
        $headers = $request->input('headers', []);
        $headers = array_merge(config('route_doc.default_headers', []), $headers);
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

    protected function sendRequest(Request $request, RouteDocModel $model)
    {
        $url = $this->formatUrl($request, $model);
        $method = $model->method;
        $body = $model->test_data['body'] ? : [];
        $headers = $model->test_data['headers'] ? : [];
        $success_code = $model->test_data['success_code'] ? : [];
        try {
            $client = new Client();
            $response = $client->$method($url, [
                'headers' => $headers,
                'json' => $body,
                'verify' => false
            ]);
            $code = $response->getStatusCode();
            $model->last_test = in_array($code, $success_code) ? 1 : 0;
            $model->test_result = $response->getBody()->getContents();
            $model->save();
        } catch (\Exception $e) {
            $code = $e->getCode();
            $model->last_test = in_array($code, $success_code) ? 1 : 0;
            $model->save();
            if ($e->getResponse()) {
                throw new \Exception($e->getResponse()->getBody()->getContents(), $code);
            } else {
                throw new \Exception($e->getMessage(), $code);
            }
        }
    }

    protected function formatUrl(Request $request, RouteDocModel $model)
    {
        $domain = $model->domain ? : $request->getSchemeAndHttpHost();
        list($scheme, $http_host) = explode("://", $domain);
        if (!$http_host) {
            $http_host = $scheme;
            $scheme = "";
        }
        $scheme = $scheme ? : $request->getScheme();
        list($host, $port) = explode(":", $http_host);
        if (!$port && !in_array($request->getPort(), ['80', '443'])) {
            $port = $request->getPort();
        }
        $url = $port ? "$scheme://$host:$port/" . $model->uri : "$scheme://$host/" . $model->uri;
        if (RouteDoc::matchUri($model, $uri_params)) {
            foreach ($uri_params[0] as $uri_param) {
                $input = $request->input('body')[$uri_param];
                if (!is_null($input)) {
                    $url = preg_replace("/\{" . $uri_param . "[?]?\}/", $input, $url);
                }
            }
        }
        return $url;
    }

    /**
     * @description update from route.php
     * @author hurs
     */
    public function anyRefresh()
    {
        $doc = new RouteDoc();
        return $doc->refresh();
    }
}