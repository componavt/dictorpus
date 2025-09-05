@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/') }}">{{ trans('messages.back_to_list') }}</a></p>
    <h2>{{ trans('messages.editing')}} {{ trans('dict.of_synsets')}} <span class='imp'>{{ $synset->name}}</span></h2>
    
    <p><b>{{ trans('dict.lang') }}:</b> {{ $synset->lang->name }}</p>
    @if (!empty($synset->pos))
    <p><b>{{ trans('dict.pos') }}:</b> {{ $synset->pos->name }}</p>
    @endif
    
    {!! Form::model($synset, array('method'=>'PUT', 'route' => array('synset.update', $synset->id))) !!}
    @include('dict.synset._form_create_edit', ['submit_title' => trans('messages.save'),
                                  'action' => 'edit'])
    {!! Form::close() !!}
@stop