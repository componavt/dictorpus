@extends('layouts.page')

@section('page_title')
{{ trans('navigation.informants') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_informant')}} <span class='imp'>"{{ $informant->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/informant/'.$informant->id) }}">{{ trans('messages.back_to_show') }}</a></p>

        @include('widgets.modal',['name'=>'modalAddPlace',
                              'title'=>trans('corpus.add_place'),
                              'submit_onClick' => 'savePlace()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.place._form_create_simple'])
        
        {!! Form::model($informant, array('method'=>'PUT', 'route' => array('informant.update', $informant->id))) !!}
        @include('corpus.informant._form_create_edit', ['action' => 'edit'])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/corpus.js')!!}
@stop
