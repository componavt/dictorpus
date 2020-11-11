@extends('layouts.page')

@section('page_title')
{{ trans('navigation.informants') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/informant/') }}">{{ trans('messages.back_to_list') }}</a></p>

        @include('widgets.modal',['name'=>'modalAddPlace',
                              'title'=>trans('corpus.add_place'),
                              'submit_onClick' => 'saveBirthPlace()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.place._form_create_simple'])
        
        {!! Form::open(array('method'=>'POST', 'route' => array('informant.store'))) !!}
        @include('corpus.informant._form_create_edit', ['action' => 'create'])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.create_new_m')])
        {!! Form::close() !!}
@stop


@section('footScriptExtra')
    {!!Html::script('js/corpus.js')!!}
@stop
