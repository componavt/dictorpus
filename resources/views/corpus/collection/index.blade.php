@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('body')
    @foreach (trans('collection.name_list') as $cid =>$t)
    <h4><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/'.$cid) }}">{{$t}}</a></h4>
{{--    <p>{!!trans('collection.about')[$cid]!!}</p> --}}
    @endforeach
@stop
