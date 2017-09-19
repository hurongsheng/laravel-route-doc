<html>
<title>@yield('title')</title>
@section('style')
    <style>
        .route_doc .table_header td {
            font-size: 14px;
            max-width: 30% !important;
        }

        .route_doc .table_body td {
            font-size: 12px;
            vertical-align: middle;
            word-wrap: break-word;
            max-width: 400px !important;
        }

        .route_doc table .btn {
            margin: 2px auto;
        }

        td.manage {
            width: 60px !important;
            padding: 5px !important;
            text-align: center;
        }

        td.params {
            max-width: 300px !important;
        }

        td.controller, td.uses {
            max-width: 250px !important;
        }

        td.updated_at, td.created_at {
            max-width: 120px !important;
            min-width: 100px !important;
        }

        td.uri {
            max-width: 250px !important;;
        }

        td table {
            width: 100%;
        }

        td table td:first-child {
            width: 25%;
        }

        .input-group-addon {
            width: 30% !important;
            text-align: left !important;
        }

        .btn_list_all {
            margin: 20px auto;
        }

    </style>
@show
<link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" rel="stylesheet">
<body class="route_doc">
<div style="padding: 10px 20px;">
    @section('btn_list')
        <div class="btn_list_all">
            @foreach($btn_list as $btn)
                @if($btn['type']=='select')
                    <select class="{{$btn['key']}}" data-key="{{$btn['key']}}">
                        <option class="option_all">--{{$btn['key']}}--</option>
                        @foreach($btn['data'] as $value)
                            @if($where[$btn['key']]===$value)
                                <option value="{{$value}}" selected>{{$value}}</option>
                            @else
                                <option value="{{$value}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                @elseif($btn['type']=='request')
                    <button class="btn btn-warning btn_list btn-sm"
                            data-uri="{{$btn['uri']}}"
                            data-method="{{$btn['method']}}">{{$btn['name']}}</button>
                @endif
            @endforeach
        </div>
    @show
    @yield('table')
    @yield('content')
</div>
</body>
<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js"></script>
<script>
    $('select').on('change', function () {
        var params = "";
        $('select').each(function (index, i) {
            var key = $(i).attr('data-key');
            var val = $(i).val();
            if (val != '--' + key + '--') {
                params = params ? params + "&" : "?";
                params = params + key + "=" + val;
            }
        });
        window.location.search = params;
    });
    $(".btn_list").on('click', function () {
        var type = $(this).attr('data-method');
        var uri = $(this).attr('data-uri');
        btn_list(type, uri);
    });

    var btn_list = function (type, url) {
        send_request(type, url, {}, function () {
            window.location.reload();
        }, function () {
            alert('something wrong');
            window.location.reload();
        });
    };
    var send_request = function (type, uri, data, success, error) {
        $.ajax({
            type: type,
            url: uri,
            data: data,
            success: success,
            error: error
        });
    }
</script>
@yield('script')
</html>