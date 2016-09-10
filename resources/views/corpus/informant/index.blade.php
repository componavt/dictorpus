<?php $list_count = $limit_num * ($page-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.informant_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.informant_list') }}</h2>
        
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/informant/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/informant/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'informant_name', 
                'value' => $informant_name,
                'size' => 15,
                'placeholder' => trans('corpus.informant_name')])
        @include('widgets.form._formitem_select', 
                ['name' => 'birth_place_id', 
                 'values' => $place_values,
                 'value' => $birth_place_id,
                 'placeholder' => trans('corpus.birth_place') ]) 
        @include('widgets.form._formitem_text', 
                ['name' => 'birth', 
                'value' => $birth,
                'size' => 4,
                'placeholder' => trans('corpus.birth_year')])
                 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        
        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'size' => 5,
                'placeholder' => trans('messages.limit_num') ]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('corpus.birth_year') }}</th>
                <th>{{ trans('corpus.birth_place') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($informants as $informant)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$informant->name_en}}</td>
                <td><a href="{{ LaravelLocalization::localizeURL('/corpus/informant/'.$informant->id) }}">{{$informant->name_ru}}</a></td>
                <td>{{$informant->birth_date}}</td>
                <td>
                    @if ($informant->birth_place)
                        {{$informant->birth_place->placeString()}}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td>
                    <a  href="{{ LaravelLocalization::localizeURL('/corpus/informant/'.$informant->id.'/edit') }}" 
                        class="btn btn-warning btn-xs btn-detail" value="{{$informant->id}}">{{ trans('messages.edit') }}</a> 
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'informant.destroy', 'id' => $informant->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $informants->appends(['limit_num' => $limit_num,
                             'informant_name' => $informant_name,
                            'birth_place_id'=>$birth_place_id,
                            'birth'=>$birth])->render() !!}

    </div>
@stop


