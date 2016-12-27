<html>
<title>route doc</title>
<style>
    .route_doc .table_header td {
        font-size: 14px;
    }

    .route_doc .table_body td {
        font-size: 12px;
        vertical-align: middle;
    }

    .route_doc table .btn {
        height: 30px;
        width: 90px;
    }

    .input-group-addon {
        width: 30% !important;
        text-align: left !important;
    }
</style>
<link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" rel="stylesheet">
<body class="route_doc">
<div style="padding: 10px;">

</div>
<div style="padding: 10px 20px;">
    <table class="table table-bordered table-condensed ">
        <tr class="table_header">
            @foreach($keys as $key)
                @if($show[$key])
                    <td class="{{$key}}">{{$show[$key]}}</td>
                @else
                    <td class="{{$key}}" hidden>{{$key}}</td>
                @endif
            @endforeach
            <td width="100">
                <button class="btn btn-warning update-all btn-sm">update doc</button>
            </td>
        </tr>

        @foreach($docs as $doc)
            <tr class="table_body" id="{{$doc['id']}}">
                @foreach($doc as $key=>$value)
                    @if($show[$key])
                        @if($key=='params'&& $value)
                            <td class="{{$key}}">
                                <table>
                                    @foreach($value as $k=>$desc)
                                        <tr>
                                            <td>{{$k}}</td>
                                            <td>{{$desc}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        @else
                            <td class="{{$key}}">{{is_array($value)?json_encode($value):$value}}</td>
                        @endif
                    @else
                        <td class="{{$key}}" hidden>{{is_array($value)?json_encode($value):$value}}</td>
                    @endif
                @endforeach
                <td class="test">
                    <button class="btn btn-info btn-sm test-this" data="{{$doc['id']}}">test route</button>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">uri params for test</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">close</button>
                <button type="button" class="btn btn-primary test-this-send">save&test</button>
            </div>
        </div>
    </div>
</div>
</body>
<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js"></script>
<script>
    var id;
    $(".test-this").on('click', function () {
        id = $(this).attr('data');
        get_params(id, function (data) {
            $(".modal-body").html(data['html']);
            $('#myModal').modal('toggle');
        })
    });
    $(".test-this-send").on('click', function () {
        var data_temp = {};
        var data = {};
        var body = {};
        $(".modal-body div.input-group-addon").each(function (index) {
            data_temp[index] = $(this).text();
        });
        $(".modal-body input.form-control").each(function (index) {
            if (data_temp[index] == 'success_code') {
                body['success_code'] = $(this).val();
            } else {
                data[data_temp[index]] = $(this).val();
            }
        });
        body['data'] = data;
        body['id'] = id;
        body['headers'] = {};
        test_this(body, function () {
            window.location.reload();
        }, function (data) {
            console.log(data);
            alert('something wrong');
            window.location.reload();
        })
    });
    $(".update-all").on('click', function () {
        update_all(function () {
            window.location.reload();
        })
    });
    var test_this = function (data, success, error) {
        $.ajax({
            type: 'POST',
            url: "test",
            data: data,
            success: success,
            error: error
        });
    };
    var update_all = function (success) {
        $.ajax({
            type: 'GET',
            url: "params-all",
            data: {},
            success: success,
            error: function () {
                alert('something wrong');
            }
        });
    };
    var get_params = function (id, success) {
        var html = '<div class="form-group"><div class="input-group"><div class="input-group-addon">' +
                '{$key}' +
                '</div><input class="form-control" value="{$value}" placeholder="{$desc}"></div></div>';
        $.ajax({
            type: 'GET',
            url: "params",
            data: {
                'id': id,
                'html': html
            },
            success: success,
            error: function () {
                alert('something wrong');
            }
        });
    }
</script>
</html>