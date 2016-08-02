<!DOCTYPE html>
<html>
<head>
    <title>{{$title}}</title>
<body class="laravel-route-doc-body">
<div class="laravel-route-doc-div">
    <table class="laravel-route-doc-table" border="1">
        <tr class="laravel-route-doc-tr">
            <td class="laravel-route-title laravel-route-url">路由</td>
            <td class="laravel-route-title laravel-route-method">方法</td>
            <td class="laravel-route-title laravel-route-params">参数</td>
            <td class="laravel-route-title laravel-route-desc">描述</td>
            <td class="laravel-route-title laravel-route-author">作者</td>
        </tr>
        @foreach($api_doc as $h3=>$routes)
            <tr class="laravel-route-doc-tr">
                <td colspan="5" class="laravel-route-doc-h3">{{$h3}}</td>
            </tr>
            @foreach($routes as $url => $method)
                @foreach($method as $item)
                    <tr class="laravel-route-doc-tr">
                        <td class="laravel-route-title laravel-route-url">{{$url}}</td>
                        <td class="laravel-route-title laravel-route-method">{{$item['method']}}</td>
                        <td class="laravel-route-title laravel-route-params">
                            @if($item['params']&&is_array($item['params']))
                                @foreach($item['params'] as $param)
                                    <i class="laravel-route-param-item" style="display:block">
                                        {{$param['param']}}{{$param['desc']?': '.$param['desc']:''}}
                                    </i>
                                @endforeach
                            @else
                                <i class="laravel-route-param-item" style="display:block">{{$item['params']?:''}}</i>
                            @endif
                        </td>
                        <td>{{$item['description']}}</td>
                        <td>{{$item['author']}}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </table>
</div>
</body>
</html>