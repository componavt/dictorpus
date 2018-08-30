@extends('layouts.page')

@section('page_title')
{{ trans('dump.dump_list') }}
@endsection

@section('body')
	<p>{{ trans('dump.readme') }}</p>
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
                <td><a href="http://dictorpus.krc.karelia.ru/{{$dump["href"]}}">{{$dump["filename"]}}</a></td>
                <td>{{$dump["date"]}}</td>
                <td>{{$dump["size"]}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
@endsection

