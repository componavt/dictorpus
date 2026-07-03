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
        @include('widgets.modal',[
            'name' => 'modalBetweenPunct',
            'title' => trans('search.punct_between'),
            'submit_id' => 'choose-between-punct',
            'submit_title' => trans('messages.choose'),
            'modal_view' => 'corpus.sentence._form_between_punct'
        ])

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
        $('.choose-pos input:checked').each(function(i) {
            poses.push($(this).val());
        });
        var posCaller = $('#insertPosTo').val();
        $('#' + posCaller).val(poses.join('|'));
        $("#modalChoosePOS").modal('hide');
    });

    $("#choose-gram").click(function(){
        var cgrams = [];
        var grams = [];
        $('.gram-category').each(function(i, c) {
            grams = [];
            $('#' + $(c).attr('id') + ' input:checked').each(function(i) {
                grams.push($(this).val());
            });
            if (grams.length > 0) {
                cgrams.push(grams.join('|'));
            }
        });
        var gramCaller = $('#insertGramTo').val();
        $('#' + gramCaller).val(cgrams.join(','));
        $("#modalChooseGram").modal('hide');
    });

    function toggleBetweenPunctTypes() {
        var mode = $('#betweenPunctMode').val();
        var $block = $('#betweenPunctTypesBlock');
        var $checks = $block.find('input[type="checkbox"]');

        if (mode == 'ignore') {
            $block.hide();
            $checks.prop('checked', false);
            $checks.prop('disabled', true);
        } else {
            $block.show();
            $checks.prop('disabled', false);
        }
    }

    window.callChooseBetweenPunct = function(step, wordNum1, wordNum2) {
        $('#betweenPunctStep').val(step);
        $('#betweenPunctWordNum1').val(wordNum1);
        $('#betweenPunctWordNum2').val(wordNum2);

        $('#betweenPunctLabel').text('Условия между словом ' + wordNum1 + ' и словом ' + wordNum2);

        var mode = $('#search_words_' + step + '__bt_mode_').val() || 'ignore';
        $('#betweenPunctMode').val(mode);

        $('.between-putype').prop('checked', false);

        $('#search_words_' + step + '__bt_types_ input[type="hidden"]').each(function() {
            var slug = $(this).val();
            $('.between-putype[value="' + slug + '"]').prop('checked', true);
        });

        toggleBetweenPunctTypes();
        $('#modalBetweenPunct').modal('show');
    };

    $('#betweenPunctMode').change(function() {
        toggleBetweenPunctTypes();
    });

    $('#choose-between-punct').click(function(){
        var step = $('#betweenPunctStep').val();
        var mode = $('#betweenPunctMode').val();
        var selectedSlugs = [];
        var selectedNames = [];

        $('.between-putype:checked').each(function() {
            selectedSlugs.push($(this).val());
            selectedNames.push($(this).data('name'));
        });

        $('#search_words_' + step + '__bt_mode_').val(mode);

        var $container = $('#search_words_' + step + '__bt_types_');
        $container.html('');

        for (var i = 0; i < selectedSlugs.length; i++) {
            $container.append(
                '<input type="hidden" name="search_words[' + step + '][bt_types][]" value="' + selectedSlugs[i] + '">'
            );
        }

        var summary = '{{ trans('search.bt_ignore') }}';
        if (mode == 'require_any') {
            summary = '{{ trans('search.bt_require_any') }}';
        } else if (mode == 'forbid_any') {
            summary = '{{ trans('search.bt_forbid_any') }}';
        }

        if (mode != 'ignore' && selectedNames.length > 0) {
            summary += ': ' + selectedNames.join(', ');
        }

        $('#between-punct-summary-' + step).text(summary);

        $('#modalBetweenPunct').modal('hide');
    });
@stop