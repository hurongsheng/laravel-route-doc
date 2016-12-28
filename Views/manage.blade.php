@extends('RouteDoc::route_doc')
@section('title', 'route doc manage')
@section('btn_list')
    @parent
@endsection
@section('table')
    <table class="table table-bordered table-condensed ">
        <tr class="table_header">
            @foreach($show as $key=>$value)
                <td class="{{$key}}">{{$value}}</td>
            @endforeach
            @foreach($keys as $key)
                @if(!$show[$key])
                    <td class="{{$key}}" hidden>{{$key}}</td>
                @endif
            @endforeach
            <td>
                manage
            </td>
        </tr>
        @foreach($docs as $doc)
            <tr class="table_body" id="{{$doc['id']}}">
                @foreach($show as $key=>$value)
                    @if($key=='params'&& $doc[$key])
                        <td class="{{$key}}">
                            <table>
                                @foreach($doc[$key] as $k=>$desc)
                                    <tr>
                                        <td>{{$k}}</td>
                                        <td>{{$desc}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    @elseif($doc[$key]==[])
                        <td class="{{$key}}"></td>
                    @else
                        <td class="{{$key}}">{{is_array($doc[$key])?json_encode($doc[$key]):$doc[$key]}}</td>
                    @endif
                @endforeach
                @foreach($doc as $key=>$value)
                    @if(!$show[$key])
                        <td class="{{$key}}" hidden>{{is_array($value)?json_encode($value):$value}}</td>
                    @endif
                @endforeach
                <td class="manage">
                    <button class="btn btn-info btn-sm test-this" data="{{$doc['id']}}">test</button>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
@section('content')
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
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
@endsection
@section('script')
    <script>
        var id;
        $(".test-this").on('click', function () {
            id = $(this).attr('data');
            $("#myModalLabel").html($("#" + id + " td.method").html() + " " + $("#" + id + " td.uri").html());
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
            body['body'] = data;
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
            send_request('POST', 'test-route', data, success, error);
        };
        var update_all = function (success) {
            send_request('GET', 'params-all', {}, success, function () {
                alert('something wrong');
            });
        };
        var get_params = function (id, success) {
            var html = '<div class="form-group"><div class="input-group"><div class="input-group-addon">' +
                    '{$key}' +
                    '</div><input class="form-control" value="{$value}" placeholder="{$desc}"></div></div>';
            send_request('GET', 'params', {'id': id, 'html': html}, success, function () {
                alert('something wrong');
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
@endsection
