<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.place_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('content')
        <h1>{{ trans('corpus.place_list') }}</h1>
            
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/place/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        @include('corpus.place._search_form',['url' => '/corpus/place/']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table-bordered table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('corpus.region') }}</th>
                <th>{{ trans('corpus.district') }}</th>
                <th>{{ trans('corpus.name') }}</th>
                <th>{{ trans('navigation.dialects') }}</th>
                <th>{{ trans('navigation.texts') }} ({{ trans('corpus.record_place') }})</th>
                <th>{{ trans('navigation.texts') }} ({{ trans('corpus.birth_place') }})</th>
                <th>{{ trans('navigation.informants') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($places as $place)
            <tr>
                <td data-th="{{ trans('corpus.region') }}">{{$place->region->name}}</td>
                <td data-th="{{ trans('corpus.district') }}">
                    @if ($place->district_id)
                        {{$place->district->name}}
                    @endif
                </td>
                <td data-th="{{ trans('corpus.title') }}">
                    @if ($place->name_en)
                    <b>{{ \App\Models\Dict\Lang::getNameByCode('en') }}:</b> {{ $place->name_en }}<br>
                    @endif
                    @if ($place->name_ru)
                    <b>{{ \App\Models\Dict\Lang::getNameByCode('ru') }}:</b> {{ $place->name_ru }}<br>
                    @endif
                    
                    @foreach($place->other_names as $other_name)
                    <b>{{ \App\Models\Dict\Lang::find($other_name->lang_id)->name }}:</b> {{ $other_name->name }}<br>
                    @endforeach
                </td>
                <td data-th="{{ trans('navigation.dialects') }}">
                    {{$place->dialectListToString()}}
                </td>
                <td class="number-cell" data-th="{{ trans('navigation.texts') }} ({{ trans('corpus.record_place') }})">
                    @if($place->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get ? $args_by_get.'&' : '?'}}search_place={{$place->id}}">
                        {{ $place->texts()->count() }}
                    </a>
                    @else 
                        0
                    @endif
                </td>
                <td class="number-cell" data-th="{{ trans('navigation.texts') }} ({{ trans('corpus.birth_place') }})">
                    @if($place->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get ? $args_by_get.'&' : '?'}}search_birth_place={{$place->id}}">
                        {{ $place->countTextBirthPlace() }}
                    </a>
                    @else 
                        0
                    @endif
                </td>
                <td class="number-cell" data-th="{{ trans('navigation.informants') }}">
                    @if($place->informants()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/informant/') }}{{$args_by_get ? $args_by_get.'&' : '?'}}search_birth_place={{$place->id}}">
                        {{ $place->informants()->count() }}
                    </a>
                    @else 
                        0
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/place/'.$place->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'place.destroy', 
                             'args'=>['id' => $place->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $places->appends($url_args)->render() !!}
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop