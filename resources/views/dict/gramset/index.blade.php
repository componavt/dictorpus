@extends('layouts.page')

@section('page_title')
{{ trans('navigation.gramsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')       
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
        @include('dict.gramset._search_form')
        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($gramsets && $numAll)
        <table class="table-bordered table-wide rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('messages.sequence_number') }}</th>              
                @foreach ($gram_fields as $field)
                <th>{{ trans('dict.'.$field) }}</th>
                @endforeach
                                
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.wordforms') }}</th>
                <th>{{ trans('corpus.texts') }} / {{ trans('corpus.words') }}</th>
                @if (User::checkAccess('ref.edit'))
                <th>{{ trans('dict.gramset_category') }}</th>
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($gramsets as $key=>$gramset)
            <tr>
                <td data-th="{{ trans('messages.sequence_number') }}">{{$gramset->sequence_number}}</td>
                @foreach ($gram_fields as $field)
                <td data-th="{{ trans('dict.'.$field) }}">
                    @if($gramset->{'gram_id_'.$field} && $gramset->{'gram'.ucfirst($field)})
                        {{$gramset->{'gram'.ucfirst($field)}->name_short}}
                    @endif
                </td>
                @endforeach

                <td data-th="{{ trans('dict.lemmas') }}" id="lemma-total-{{$gramset->id}}"></td>
                <td data-th="{{ trans('dict.wordforms') }}" id="wordform-total-{{$gramset->id}}"></td>
                <td data-th="{{ trans('corpus.texts') }} / {{ trans('corpus.words') }}" id="text_word-total-{{$gramset->id}}"></td>
                
                @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('dict.gramset_category') }}">
                  {{ $gramset->gramsetCategory ? $gramset->gramsetCategory->name : ''}}
                </td>
                
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/dict/gramset/'.$gramset->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'gramset'])
                </td>                
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
            {!! $gramsets->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/dict.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    @foreach($gramsets as $key=>$gramset)
        loadCount('#lemma-total-{{$gramset->id}}', '{{ LaravelLocalization::localizeURL('/dict/gramset/'.$gramset->id.'/lemma_count/'.$url_args['search_lang'].'/'.$url_args['search_pos']) }}');
        loadCount('#wordform-total-{{$gramset->id}}', '{{ LaravelLocalization::localizeURL('/dict/gramset/'.$gramset->id.'/wordform_count/'.$url_args['search_lang'].'/'.$url_args['search_pos']) }}');
        loadCount('#text_word-total-{{$gramset->id}}', '{{ LaravelLocalization::localizeURL('/dict/gramset/'.$gramset->id.'/text_word_count/'.$url_args['search_lang'].'/'.$url_args['search_pos']) }}');
    @endforeach
@stop
