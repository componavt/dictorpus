@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    {!! css('select2.min') !!}
    {!! css('lemma') !!}
    {!! css('synsets') !!}
    <link rel="stylesheet" href="{{ asset('css/vendor/trix.css') }}">
    {!! css('synset_text_fields') !!}
@stop

@section('body')      
    @include('dict.synset._'.$action)
@stop

@section('footScriptExtra')
    {!! js('select2.min') !!}
    {!! js('list_change') !!}
    {!! js('synset') !!}
    {!! js('synset_text_fields') !!}
    <script src="{{ asset('js/vendor/trix.umd.min.js') }}"></script>
@stop

@section('jqueryFunc')
    selectSynsetMembers(".select-member", '{{ $action === 'edit' ? $synset->lang_id : $lang->id }}', 'введите карельское или русское слово');
    
    $(document).on('change.synsetDominant', '.check-dominant', function() {
        $('.meaning-row').css('font-weight', 'normal');
        $(this).closest('.meaning-row').css('font-weight', 'bold');
    });

    var restoredDominant = $('#synset-restored-dominant');
    if (restoredDominant.length) {
        $('.check-dominant').prop('checked', false);
        $('.meaning-row').css('font-weight', 'normal');
        $('.check-dominant').filter(function() {
            return this.value === restoredDominant.val();
        }).prop('checked', true).closest('.meaning-row').css('font-weight', 'bold');
    }

    initSynsetCollocationsView();
    initSynsetCollocationComparison();
    initSynsetCollocationNavigation();
@stop
