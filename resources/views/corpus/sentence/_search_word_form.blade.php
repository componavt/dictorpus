<div class="row">
    <div class="col-md-3">
        @include('widgets.form.formitem._text', 
                ['name' => "search_words[$count][w]", 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-word')",
                 'value' => $word['w'] ?? null,
                 'title'=> trans('corpus.word').' '.$count
                ])
    </div>
    <div class="col-md-3">
        @include('widgets.form.formitem._text', 
                ['name' => "search_words[$count][p]", 
                 'help_func' => "callHelp('help-add-pos')",
                 'value' => $word['p'] ?? null,
                 'title'=> trans('dict.part_of_speech')
                ])                               
        <i class='add-more add-pos-codes fas fa-plus-circle fa-lg' onClick='callChoosePOS(this)' data-for='search_words_{{$count}}__p_'></i>
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._text', 
                ['name' => "search_words[$count][g]", 
                 'help_func' => "callHelp('help-add-grams')",
                 'value' => $word['g'] ?? null,
                 'title'=> trans('dict.gramsets')
                ])                               
        <i class='add-more add-pos-codes fas fa-plus-circle fa-lg' onClick='callChooseGram(this)' data-for='search_words_{{$count}}__g_'></i>
    </div>
    <div class="col-sm-2">
        <div id="distance{{$count+1}}"  class="form-group"{!! !isset($search_words[$count+1]['d_f']) ?  ' style="display:none"' : '' !!}>
            <label for="search_words[1][g]">{{trans('search.distance')}}&nbsp;</label>
            <i class="help-icon far fa-question-circle fa-lg" onclick="callHelp('help-distance')"></i>
            <div style="display: flex">
                <span>{{trans('search.d_f')}}&nbsp;&nbsp;</span>
                <input class="form-control" name="search_words[{{$count+1}}][d_f]" type="text" value="{{$search_words[$count+1]['d_f'] ?? 1}}" {{isset($search_words[$count+1]['d_f']) ? '' : 'disabled'}}>
                <span>&nbsp;&nbsp;{{trans('search.d_t')}}&nbsp;&nbsp;</span>
                <input class="form-control" name="search_words[{{$count+1}}][d_t]" type="text" value="{{$search_words[$count+1]['d_t'] ?? 1}}" {{isset($search_words[$count+1]['d_f']) ? '' : 'disabled'}}>
            </div>            
        </div>    
        @if (!user_corpus_edit() && $count<2 || user_corpus_edit() && $count<3)
        <a title="{{trans('search.add_word')}}" style="cursor: pointer; padding-top: 28px; display: {{ (isset($search_words[$count+1]['d_f']) ?  'none' : 'block') }}" onClick='addSentenceWordsFields(this)' data-count='{{ $count+1 }}'>
            <i class="far fa-plus-square fa-2x"></i>
        </a>
        @endif
    </div>
</div>
