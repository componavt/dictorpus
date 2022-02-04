@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('body')
    @foreach (trans('collection.name_list') as $cid =>$title)
    <h4><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/'.$cid) }}">{{$title}}</a></h4>
    <p>{!!trans('collection.about')[$cid]!!}</p>
    @endforeach
@stop
