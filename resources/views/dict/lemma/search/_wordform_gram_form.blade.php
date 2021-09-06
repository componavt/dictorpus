    <div class="col-sm-4">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_wordforms['.$count.']', 
                 'help_func' => "callHelp('help-text-fields')",
                 'special_symbol' => true,
                 'value' => $url_args['search_wordforms'][$count] ?? NULL,
                 'attributes'=>['placeholder'=>trans('dict.wordform').' '.$count]])
    </div>
    <div class="col-sm-5">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_gramsets['.$count.']', 
                 'values' =>$gramset_values,
                 'value' =>$url_args['search_gramsets'][$count] ?? NULL,
                 'is_multiple' => false,
                 'class'=>'select-gramset form-control'])
    </div>
@if ($with_button ?? true)
    <div class="col-sm-3">
        <a title="добавить в поиск еще словоформу" style='cursor: pointer' onClick='addWordformGramFields(this)' data-count='{{ $count+1 }}'>
            <i class="far fa-plus-square fa-2x"></i>
        </a>
    </div>
@endif
