<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.informant_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('content')
        <h1>{{ trans('corpus.informant_list') }}</h1>
        
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/informant/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        @include('corpus.informant._search_form',['url' => '/corpus/informant/']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table-bordered table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('corpus.birth_year') }}</th>
                <th>{{ trans('corpus.birth_place') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($informants as $informant)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$informant->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$informant->name_ru}}</td>
                <td data-th="{{ trans('corpus.birth_year') }}">{{$informant->birth_date}}</td>
                <td data-th="{{ trans('corpus.birth_place') }}">
                    @if ($informant->birth_place)
                        {{$informant->birth_place->placeString()}}
                    @endif
                </td>
                <td data-th="{{ trans('navigation.texts') }}">
                   @if($informant->texts())
                   <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get ? $args_by_get.'&' : '?'}}search_informant={{$informant->id}}">
                       {{ $informant->texts()->count() }} 
                   </a>
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/informant/'.$informant->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'informant.destroy', 
                             'args'=>['id' => $informant->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $informants->appends($url_args)->render() !!}

    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


