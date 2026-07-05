<div class="row">
    <div class="col-md-2">
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

    <div class="col-md-3">
        <div id="distance{{$count+1}}" class="form-group"{!! !isset($search_words[$count+1]['d_f']) ? ' style="display:none"' : '' !!}>
            <div style="margin-bottom: 6px;">
                <label for="search_words[1][g]" style="margin-bottom: 0;">
                    {{ trans('search.distance') }}
                </label>
                <span style="margin-left: 6px;">
                    <i class="help-icon far fa-question-circle fa-lg" onclick="callHelp('help-distance')"></i>
                </span>
            </div>

            <div style="display: flex; align-items: flex-start;">
                <div style="display: flex; align-items: center; padding-top: 12px;">
                    <span>{{ trans('search.d_f') }}&nbsp;&nbsp;</span>
                    <input class="form-control"
                           name="search_words[{{$count+1}}][d_f]"
                           type="text"
                           value="{{$search_words[$count+1]['d_f'] ?? 1}}"
                           {{isset($search_words[$count+1]['d_f']) ? '' : 'disabled'}}
                           style="width: 48px; padding-left: 6px; padding-right: 6px;">

                    <span>&nbsp;&nbsp;{{ trans('search.d_t') }}&nbsp;&nbsp;</span>
                    <input class="form-control"
                           name="search_words[{{$count+1}}][d_t]"
                           type="text"
                           value="{{$search_words[$count+1]['d_t'] ?? 1}}"
                           {{isset($search_words[$count+1]['d_f']) ? '' : 'disabled'}}
                           style="width: 48px; padding-left: 6px; padding-right: 6px;">
                </div>

                <a class="btn btn-default"
                   href="javascript:void(0);"
                   onclick="callChooseBetweenPunct({{$count+1}}, {{$count}}, {{$count+1}})"
                   title="{{ trans('search.punct_between') }}"
                   style="margin-left: 8px; width: 64px; height: 78px; padding: 10px 4px; font-size: 11px; line-height: 1.15; text-align: center; white-space: normal; vertical-align: top;">
                    Знаки<br>пунктуации
                </a>
            </div>
        </div>

        @if (!user_corpus_edit() && $count<2 || user_corpus_edit() && $count<3)
        <a title="{{trans('search.add_word')}}"
           style="cursor: pointer; padding-top: 28px; display: {{ (isset($search_words[$count+1]['d_f']) ? 'none' : 'block') }}"
           onClick='addSentenceWordsFields(this)'
           data-count='{{ $count+1 }}'>
            <i class="far fa-plus-square fa-2x"></i>
        </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="small text-muted between-punct-summary"
             id="between-punct-summary-{{$count+1}}"
             style="margin-top: -6px; margin-bottom: 12px; padding-left: 15px;">
            @if (($search_words[$count+1]['bt_mode'] ?? 'ignore') == 'require_any')
                {{ trans('search.bt_require_any') }}
            @elseif (($search_words[$count+1]['bt_mode'] ?? 'ignore') == 'forbid_any')
                {{ trans('search.bt_forbid_any') }}
            @else
                {{ trans('search.bt_ignore') }}
            @endif
        </div>
    </div>
</div>

@if ($count > 1)
    <input type="hidden"
           name="search_words[{{$count}}][bt_mode]"
           id="search_words_{{$count}}__bt_mode_"
           value="{{ $word['bt_mode'] ?? 'ignore' }}">

    <div id="search_words_{{$count}}__bt_types_" style="display:none;">
        @if (!empty($word['bt_types']) && is_array($word['bt_types']))
            @foreach ($word['bt_types'] as $slug)
                <input type="hidden"
                       name="search_words[{{$count}}][bt_types][]"
                       value="{{$slug}}">
            @endforeach
        @endif
    </div>
@endif