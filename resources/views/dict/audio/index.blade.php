<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.audios') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/essential_audio_circle_mini.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
{{--        @include('dict.audio._search_form',['url' => '/dict/audio']) --}}

        @if ($audios)
        <table id="audiosTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('corpus.informant') }}</th>
                <th>{{ trans('messages.created_at') }}</th>
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audios as $audio)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    @foreach($audio->lemmas as $lemma)
                    <a href="{{ LaravelLocalization::localizeURL('dict/lemma/'.$lemma->id) }}">
                        {{$lemma->lemma}}
                    </a>
                    @endforeach
                </td>
                <td data-th="{{ trans('corpus.informant') }}">
                    {{$audio->informant->name}}
                </td>
                <td data-th="{{ trans('messages.created_at') }}">
                    {{$audio->created_at}}
                </td>
                <td data-th="{{ trans('messages.actions') }}">
                    <div class='audio-button'>
                @include('widgets.audio_simple', ['route'=>$audio->url()])
                    </div>
                @if (User::checkAccess('dict.edit'))
                    @include('widgets.form.button._delete', 
                             ['is_button'=>false, 
                              'without_text' => true,
                              'route' => 'audio.destroy', 
                              'args'=>['id' => $audio->id]])
                @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    recDelete('{{ trans('messages.confirm_delete') }}');
    
    $('#audiosTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 3, "desc" ]]
    } );
@stop

