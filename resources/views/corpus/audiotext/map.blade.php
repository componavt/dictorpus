@extends('layouts.page')

@section('page_title')
{{ trans('navigation.dialect_map') }}
@stop

@section('headExtra')
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>
      {!!Html::style('css/map.css')!!}
@stop

@section('body')
    @include('widgets.leaflet.map')
@stop

@section('footScriptExtra')
    @include('widgets.leaflet.map_script')
@endsection
