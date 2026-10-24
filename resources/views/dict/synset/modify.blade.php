@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    {!! css('select2.min') !!}
    {!! css('lemma') !!}
@stop

@section('body')      
    @include('dict.synset._'.$action)
@stop

@section('footScriptExtra')
    {!! js('select2.min') !!}
    {!! js('list_change') !!}
    {!! js('synset') !!}
@stop

@section('jqueryFunc')
    selectSynsetMembers(".select-member", '{{ $url_args['search_lang'] }}', 'введите карельское или русское слово');
    
    $(".check-dominant").on('click', function() {
        id = $(this).data('id');
        $(".meaning-row").css('font-weight', 'normal');
        $("#meaning-"+id).css('font-weight', 'bold');
    });

    // Переключает представления сочетаемости без отправки формы.
    $('.synset-collocations-view').on('click', function () {
        var button = $(this);
        var container = button.closest('.synset-collocations');
        var show_matrix = button.attr('data-view') === 'matrix';

        container.find('#synset-collocations-words')
            .prop('hidden', show_matrix);

        container.find('#synset-collocations-matrix')
            .prop('hidden', !show_matrix);

        container.find('.synset-collocations-view')
            .removeClass('active')
            .attr('aria-pressed', 'false');

        button
            .addClass('active')
            .attr('aria-pressed', 'true');
    });
@stop
