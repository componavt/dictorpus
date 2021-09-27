@extends('layouts.page')

@section('page_title')
{{ trans('corpus.gram_search') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/buttons.css')!!}
@stop

@section('body')
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.text._search'])
        @include('widgets.modal',['name'=>'modalChoosePOS',
                              'title'=>trans('search.choose_pos'),
                              'submit_id' => 'choose-pos',
                              'submit_title' => trans('messages.choose'),
                              'modal_view'=>'corpus.sentence._form_choose_pos'])
        @include('widgets.modal',['name'=>'modalChooseGram',
                              'title'=>trans('search.choose_gram'),
                              'submit_id' => 'choose-gram',
                              'submit_title' => trans('messages.choose'),
                              'modal_view'=>'corpus.sentence._form_choose_gram'])
        @include('corpus.sentence._search_form', ['url'=>LaravelLocalization::localizeURL('/corpus/sentence/results')])                                  
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
    $(".multiple-select-lang").select2();
    $(".multiple-select-corpus").select2();
    selectGenre();
    selectWithLang('.multiple-select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    
    $("#choose-pos").click(function(){
        var poses = [];
        $('.choose-pos input:checked').each(function( i ) {
            poses.push($(this).val());
        });        
        var posCaller = $('#insertPosTo').val();
        $('#'+posCaller).val(poses.join('|'));
//console.log(posCaller);                
        $("#modalChoosePOS").modal('hide');    

    });
    
    $("#choose-gram").click(function(){
        var cgrams = [];
        var grams = [];
        $('.gram-category').each(function(i, c) {
//console.log($(c).attr('id'));        
            grams = [];
console.log('#'+$(c).attr('id')+' input:checked');            
            $('#'+$(c).attr('id')+' input:checked').each(function( i ) {
                grams.push($(this).val());
            });  
            if (grams.length > 0) {
                cgrams.push(grams.join('|'));
            }
        });        
        var gramCaller = $('#insertGramTo').val();
        $('#'+gramCaller).val(cgrams.join(','));
        $("#modalChooseGram").modal('hide');    

    });
@stop
