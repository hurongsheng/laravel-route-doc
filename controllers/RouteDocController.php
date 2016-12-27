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
     * @description
     * @param Request $request
     * @author hurs
     */
    public function getParamsAll(Request $request)
    {
        $this->postRefresh();
        $models = RouteDocModel::where('state', RouteDocModel::STATE_WORK)->get();
        foreach ($models as $model) {
            try {
                $this->handleModel($model);
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
        $model = $this->handleModel($model);
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

    protected function handleModel(RouteDocModel $model)
    {
        $params = $doc = [];
        if ($model->controller) {
            list($controller, $method) = explode("@", $model->controller);
            $analyse = new analyse_code($controller);
            try {
                $doc = $analyse->getFunctionDocument($method, $model->uri, $fixed_uri);
                $model->uri = $fixed_uri;
                $model->description = $doc['description'] ? : '';
                $model->author = $doc['author'] ? : '';
            } catch (\Exception $e) {
                $model->state = RouteDocModel::STATE_DELETE;
            }
        }
        if (preg_match_all('/(?<=[{])[\S]+?(?=[}])/', $model->uri, $uris)) {
            foreach ($uris[0] as $uri) {
                $uri_1 = str_replace("?", "", $uri);
                $desc = ($uri == $uri_1) ? $uri_1 : $uri_1 . "(optional)";
                $params[$uri_1] = $doc['params'][$uri_1] ? : $desc;
                if ($model->where && $model->where[$uri_1]) {
                    $params[$uri_1] = $params[$uri_1] . '/' . $model->where[$uri_1] . '/';
                }
            }
        };
        $model->params = array_merge($doc['params'] ? : [], $params);
        $model->test_data = $model->test_data ? : [];
        $model->save();
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
            if ($e->getResponse()) {
                throw new \Exception($e->getResponse()->getBody()->getContents(), $code);
            } else {
                throw new \Exception($e->getMessage(), $code);
            }
        }
    }

    public function postRefresh()
    {
        $doc = new RouteDoc();
        $doc->refresh();
    }
}