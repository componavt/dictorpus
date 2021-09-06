        {!! Form::open(['url' => '/dict/lemma/by_wordforms', 
                             'method' => 'get']) 
        !!}
        
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
    </div>
    <div class="col-sm-5">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialects',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialects'],
                 'help_func' => "callHelp('help-dialect')",
                 'class'=>'select-dialects form-control']) 
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_pos', 
                 'values' =>$pos_values,
                 'value' =>$url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
</div>    
<div id="search-wordforms" class="row">
    @foreach (array_keys($url_args['search_wordforms']) as $count)    
        @include('dict.lemma.search._wordform_gram_form', [
            'count'=>$count, 
            'with_button' =>array_key_last($url_args['search_wordforms']) == $count])
    @endforeach
</div>    
<div class="row">
    <div class="col-sm-4 search-button-b">       
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5, 'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
    </div>
</div>    

        {!! Form::close() !!}
