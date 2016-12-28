@extends('RouteDoc::route_doc')
@section('title', 'route doc list')
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
            </tr>
        @endforeach
    </table>
@endsection