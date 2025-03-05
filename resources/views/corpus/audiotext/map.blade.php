@extends('layouts.page')

@section('page_title')
{{ trans('navigation.audio_map') }}
@stop

@section('headExtra')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>
      {!!Html::style('css/map.css')!!}
@stop

@section('body')
    <h4>{{ trans('corpus.audio_map_info') }}</h4>
    <div style='display: flex; font-size: 16px; align-items: center'>
        <img src="/images/rgo.png" style="padding-right: 20px;">
        <p>{{ trans('corpus.rgo_info') }}</p>
    </div>
    <div style='clear: both; margin-top: 20px'>
    @include('widgets.leaflet.map')
    </div>
@stop

@section('footScriptExtra')
    @include('widgets.leaflet.map_script')
@endsection
