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
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        @if ($gramsets && $gramsets->count())
        <br>
        <table class="table">
        <thead>
            <tr>
                <th>No</th>              
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
                <td>{{$key+1}}</td>
                @foreach ($gram_fields as $field)
                <td>
                    @if($gramset->{'gram_id_'.$field})
                        {{$gramset->{'gram'.ucfirst($field)}->getNameWithShort()}}
                    @endif
                </td>
                @endforeach
                
                <td>{{ $gramset->wordforms()->count()}}</td>

                @if (User::checkAccess('ref.edit'))
                <td>
                    @include('widgets.form._button_edit', ['route' => '/dict/gramset/'.$gramset->id.'/edit',
                                                           'is_button' => true,
                                                           'without_text' => true])
                </td>
                <td>
                    @include('widgets.form._button_delete', ['route' => 'gramset.destroy', 'id' => $gramset->id,
                                                             'is_button' => true,
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
