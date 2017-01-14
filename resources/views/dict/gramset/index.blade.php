@extends('layouts.master')

@section('title')
{{ trans('navigation.gramsets') }}
@stop

@section('content')
        <h2>{{ trans('navigation.gramsets') }}</h2>
        
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/dict/gramset/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_select', 
                ['name' => 'pos_id', 
                 'values' =>$pos_values,
                 'value' =>$pos_id,
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
        @include('widgets.form._formitem_select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        @if ($gramsets && $gramsets->count())
        <p>{{ trans('messages.founded_records', ['count'=>$gramsets->count()]) }}</p>
        <br>
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('messages.sequence_number') }}</th>              
                @foreach ($gram_fields as $field)
                <th>{{ trans('dict.'.$field) }}</th>
                @endforeach
                
                <th>{{ trans('dict.wordforms') }}</th>
                
                @if (User::checkAccess('ref.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($gramsets as $key=>$gramset)
            <tr>
                <td>{{$gramset->sequence_number}}</td>
                @foreach ($gram_fields as $field)
                <td>
                    @if($gramset->{'gram_id_'.$field})
                        {{$gramset->{'gram'.ucfirst($field)}->getNameWithShort()}}
                    @endif
                </td>
                @endforeach
                
                <td>{{ $gramset->wordforms($pos_id,$lang_id)->count()}}</td>

                @if (User::checkAccess('ref.edit'))
                <td>
                    @include('widgets.form._button_edit', ['route' => '/dict/gramset/'.$gramset->id.'/edit',
                                                           'is_button' => true,
                                                           'url_args' => $url_args,
                                                           'without_text' => true])
                </td>
                <td>
                    @include('widgets.form._button_delete', ['route' => 'gramset.destroy', 'id' => $gramset->id,
                                                             'is_button' => true,
                                                             'url_args' => $url_args,
                                                             'without_text' => true])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/gramset');
@stop
