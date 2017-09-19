@extends('RouteDoc::route_doc')
@section('title', 'route doc manage')
@section('btn_list')
@endsection
@section('table')
    <table class="table table-bordered table-condensed ">
        @foreach($show as $key=>$value)
            <tr class="table_body" id="{{$doc['id']}}">
                <td>{{$value}}</td>
                @if($key=='params'&& $doc[$key])
                    <td class="{{$key}}">
                        <table>
                            @foreach($doc[$key] as $k=>$desc)
                                <tr>
                                    <td>{{ucfirst($doc['param_types'][$k])}}</td>
                                    <td>{{$k}}</td>
                                    <td>{{$desc}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                @elseif($key=='test_result_desc')
                    <td class="{{$key}}">
                        <table>
                            @foreach($doc[$key] as $k=>$desc)
                                <tr>
                                    <td>{{ucfirst($doc['param_types'][$k])}}</td>
                                    <td>{{$k}}</td>
                                    <td>{{$desc}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                @elseif($key=='return')
                    <td class="{{$key}}">{!! str_replace("|","<br/>",$doc[$key]) !!}</td>
                @elseif($doc[$key]==[])
                    <td class="{{$key}}"></td>
                @else
                    <td class="{{$key}}">{{is_array($doc[$key])?json_encode($doc[$key]):$doc[$key]}}</td>
                @endif
            </tr>
        @endforeach
    </table>
@endsection