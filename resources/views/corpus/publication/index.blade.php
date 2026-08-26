<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.source_publications') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/publication/create') }}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/publication/', 
                             'method' => 'get']) 
        !!}
        <div class="search-form row">
            <div class="col-sm-2">
            @include('widgets.form.formitem._text', 
                ['name' => 'search_authors', 
                'value' => $url_args['search_authors'] ?? '',
                'title'=>trans('corpus.authors')
                ])
            </div>
            <div class="col-sm-2">
            @include('widgets.form.formitem._text', 
                ['name' => 'search_title', 
                'value' => $url_args['search_title'] ?? '',
                'title'=> trans('corpus.title')
                ])
            </div>
            <div class="col-md-2">
            @include('widgets.form.formitem._text', 
                    ['name' => 'search_year_from', 
                     'value' => $url_args['search_year_from'] ?? '',
                     'title' => trans('search.year_from')
                    ])                               
            </div>
            <div class="col-md-2">
            @include('widgets.form.formitem._text', 
                    ['name' => 'search_year_to', 
                     'help_func' => 'callHelpYear()',
                     'value' => $url_args['search_year_to'] ?? '',
                     'title' => trans('search.year_to')
                    ])                               
            </div>
            @include('widgets.form._search_div', ['rows'=>2])
        </div>
        {!! Form::close() !!}

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.authors') }}</th>
                <th>{{ trans('corpus.title') }}</th>
                <th>{{ trans('corpus.addition_info') }}</th>
                <th>{{ trans('messages.year') }}</th>
                <th>{{ trans('corpus.pubparts') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($publications as $publication)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.authors') }}">
                    {{ $publication->authors }}
                </td>
                <td data-th="{{ trans('corpus.title') }}">
                    <a href="{{ route('publication.show', $publication->id) }}{{ $args_by_get }}">
                        {{ $publication->title }}
                    </a>
                </td>
                <td data-th="{{ trans('corpus.addition_info') }}">
                    {{ $publication->addition_info }}
                </td>
                <td data-th="{{ trans('messages.year') }}">
                    {{ $publication->year }}
                </td>
                <td data-th="{{ trans('corpus.pubparts') }}" style="text-align: center">
                    {{ $publication->pubparts()->count() }}
                </td>
                <td data-th="{{ trans('navigation.texts') }}" style="text-align: center">
                    @if($publication->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text?search_publication=') }}{{ $publication->id}}">{{ $publication->texts()->count() }}</a>
                    @else
                    0
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}" style="text-align: center">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/publication/'.$publication->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'publication.destroy', 
                             'args'=>['id' => $publication->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $publications->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


