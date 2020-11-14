@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_text')}} <span class='imp'>"{{ $text->title}}"</span></h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a> |            
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/form') }}">? {{ trans('navigation.help') }}</a>
        </p>

        @include('widgets.modal',['name'=>'modalAddInformant',
                              'title'=>trans('corpus.add_informant'),
                              'submit_onClick' => 'saveInformant()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.informant._form_create_edit'])
                              
        @include('widgets.modal',['name'=>'modalAddDistrict',
                              'title'=>trans('corpus.add_district'),
                              'submit_onClick' => 'saveDistrict()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.district._form_create_edit'])
        
        @include('widgets.modal',['name'=>'modalAddPlace',
                              'title'=>trans('corpus.add_place'),
                              'submit_onClick' => 'savePlace()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.place._form_create_simple'])
        
        @include('widgets.modal',['name'=>'modalAddRecorder',
                              'title'=>trans('corpus.add_recorder'),
                              'submit_onClick' => 'saveRecorder()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.recorder._form_create_edit'])
        
        
        {!! Form::model($text, array('method'=>'PUT', 'route' => array('text.update', $text->id))) !!}
        @include('corpus.text._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/corpus.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    $(".multiple-select").select2();
    
    selectDialect('lang_id');
    
    $('.text-unlock').click(function() {
        $(this).hide();
        $('#text').prop('readonly',false);
    });
@stop
