@extends('layouts.master')

@section('title')
{{ trans('dump.dump_list') }}
@endsection

@section('content')
        <h2>{{ trans('dump.dump_list') }}</h2>

        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('dump.filename') }}</th>
                <th>{{ trans('dump.date') }}</th>
                <th>{{ trans('dump.size') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dumps as $dump)
            <tr>
                <td><a href="{{$dump["href"]}}">{{$dump["filename"]}}</a></td>
                <td>{{$dump["date"]}}</td>
                <td>{{$dump["size"]}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div> 
@endsection

