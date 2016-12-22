<html>
<title>route doc</title>
<style>
    .route_doc .table_header td {
        font-size: 14px;
    }

    .route_doc .table_body td {
        font-size: 12px;
    }

    .route_doc table .btn {
        height: 30px;
        width: 70px;
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
            <td width="80">
                <button class="btn btn-info test-all btn-sm">test all</button>
            </td>
        </tr>

        @foreach($docs as $doc)
            <tr class="table_body" id="{{$doc['id']}}">
                @foreach($doc as $key=>$value)
                    @if($show[$key])
                        <td class="{{$key}}">{{is_array($value)?json_encode($value):$value}}</td>
                    @else
                        <td class="{{$key}}" hidden>{{is_array($value)?json_encode($value):$value}}</td>
                    @endif
                @endforeach
                <td class="test">
                    <button class="btn btn-info btn-sm test-this" data="{{$doc['id']}}">test this</button>
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
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">close</button>
                <button type="button" class="btn btn-primary">save&test</button>
            </div>
        </div>
    </div>
</div>
</body>
<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js"></script>
<script>
    $(".test-this").on('click', function () {
        var id = $(this).attr('data');
        get_params(id, function (data) {
            console.log(data);
            $('#myModal').modal('toggle');
        })
    });
    var get_params = function (id, success) {
        $.ajax({
            type: 'GET',
            url: "params",
            data: {'id': id},
            success: success,
            error: function () {
                alert('something wrong');
            }
        });
    }
</script>
</html>