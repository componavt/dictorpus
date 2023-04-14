<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.places') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
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

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('corpus.region') }}</th>
                <th>{{ trans('corpus.district') }}</th>
                <th>{{ trans('corpus.name') }}</th>
                <th>{{ trans('corpus.latitude') }}</th>
                <th>{{ trans('corpus.longitude') }}</th>
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
                    @if ($place->name_ru)
                    {{ $place->name_ru }} (<i>ru</i>) <br>
                    @endif                    
                    @if ($place->name_en)
                    {{ $place->name_en }} (<i>en</i>) <br>
                    @endif
                    @foreach($place->other_names as $other_name)
                    {{ $other_name->name }} (<i>{{ \App\Models\Dict\Lang::find($other_name->lang_id)->code }}</i>)<br>
                    @endforeach
                </td>
                
                <td data-th="{{ trans('corpus.latitude') }}">
                    {{ $place->latitude ? sprintf("%.05f\n", $place->latitude) : '' }}
                </td>
                <td data-th="{{ trans('corpus.longitude') }}">
                    {{ $place->longitude ? sprintf("%.05f\n", $place->longitude) : '' }}
                </td>
                
                <td data-th="{{ trans('navigation.dialects') }}">
                    {{$place->dialectListToString()}}
                </td>
                <td class="number-cell" data-th="{{ trans('navigation.texts') }} ({{ trans('corpus.record_place') }})">
                    @if($place->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}?search_place={{$place->id}}">
                        {{ $place->texts()->count() }}
                    </a>
                    @else 
                        0
                    @endif
                </td>
                <td class="number-cell" data-th="{{ trans('navigation.texts') }} ({{ trans('corpus.birth_place') }})">
                    @if($place->countTextBirthPlace())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}?search_birth_place={{$place->id}}">
                        {{ $place->countTextBirthPlace() }}
                    </a>
                    @else 
                        0
                    @endif
                </td>
                <td class="number-cell" data-th="{{ trans('navigation.informants') }}">
                    @if($place->informants()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/informant') }}?search_birth_place={{$place->id}}">
                        {{ $place->informants()->count() }}
                    </a>
                    @else 
                        0
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/corpus/place/'.$place->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'place'])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $places->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop