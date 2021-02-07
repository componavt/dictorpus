        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'corpus_id', 
                 'values' =>$corpus_values,
                 'title' => trans('corpus.corpus')]) 
                 
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'title' => trans('navigation.dialects'),
                 'class'=>'select-dialect form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'genres', 
                 'values' =>$genre_values,
                 'value' => $genre_value,
                 'title' => trans('navigation.genres'),
                 'class'=>'multiple-select form-control'
            ])
    </div>
</div>                 
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lang_id']])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'title', 
                 'title'=>trans('corpus.title')])
        
        <?php $attr = ['id'=>'text']; ?>
        @if ($action=='edit' && $text->meanings()->wherePivot('relevance','<>',1)->count())
            <?php $attr[] = 'readonly'; ?>
            <p class="warning text-has-checked-meaning">
                {{trans('corpus.text_has_checked_meaning')}}
                <button type="button" class="btn btn-info text-unlock">
                    {{trans('corpus.unlock')}}
                </button>

            </p>
        @endif
        @include('widgets.form.formitem._textarea', 
                ['name' => 'text', 
                 'special_symbol' => true,
                 'title'=>trans('corpus.text'),
                 'help_text' =>trans('corpus.text_help'),
                 'attributes' => $attr,
                ])
        @if ($action == 'edit')
            @include('widgets.form.formitem._textarea', 
                    ['name' => 'text_xml', 
                     'special_symbol' => true,
                     'title'=>trans('corpus.text_xml')])
        @endif
        
        @include('widgets.form.formitem._select2', 
                ['name' => 'authors', 
                 'values' =>$author_values,
                 'value' => $author_value ?? null,
                 'call_add_onClick' => 'addAuthor()',
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.author')]) 
                 
        {{-- EVENT --}}
        <?php if ($action=='create') { $informant_value = NULL; } ?>        
        @include('widgets.form.formitem._select2', 
                ['name' => 'event.informants', 
                 'values' =>$informant_values,
                 'value' => $informant_value,
                 'call_add_onClick' => 'addInformant()',
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.informant')]) 
        <?php $event_date_value = ($action=='edit' && $text->event) ? ($text->event->date) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'event.date', 
                 'value' => $event_date_value,
                 'size' => 4,
                 'title'=>trans('corpus.record_year')])
        <?php $event_place_value = ($action=='edit' && $text->event) ? ($text->event->place_id) : NULL; ?>
        @include('widgets.form.formitem._select', 
                ['name' => 'event.place_id', 
                 'values' =>$place_values,
                 'value' => $event_place_value,
                 'call_add_onClick' => "addPlace('event_place_id')",
                 'call_add_title' => trans('messages.create_new_g'),
                 'title' => trans('corpus.record_place')]) 
        <?php if ($action=='create') { $recorder_value = NULL; } ?>        
        @include('widgets.form.formitem._select2',
                ['name' => 'event.recorders', 
                 'values' =>$recorder_values,
                 'value' => $recorder_value,
                 'call_add_onClick' => 'addRecorder()',
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.recorded'),
                 'class'=>'multiple-select form-control'
            ])
                
@include('widgets.form.formitem._submit', ['title' => $submit_title])
    </div>
    <div class="col-sm-6">
        <?php $transtext_lang_id_value = ($action=='edit' && $text->transtext) ? ($text->transtext->lang_id) : NULL; ?>
        @include('widgets.form.formitem._select', 
                ['name' => 'transtext.lang_id', 
                 'values' =>$lang_values,
                 'value' => $transtext_lang_id_value,
                 'title' => trans('corpus.transtext_lang')]) 
        <?php $transtext_title_value = ($action=='edit' && $text->transtext) ? ($text->transtext->title) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'transtext.title', 
                 'special_symbol' => true,
                 'value' => $transtext_title_value,
                 'title'=>trans('corpus.transtext_title')])
        <?php $transtext_text_value = ($action=='edit' && $text->transtext) ? ($text->transtext->text) : NULL; ?>
        @include('widgets.form.formitem._textarea', 
                ['name' => 'transtext.text', 
                 'help_text' =>trans('corpus.text_help'),
                 'special_symbol' => true,
                 'value' => $transtext_text_value,
                 'title'=>trans('corpus.transtext_text')])
        @if ($action=='edit')
            <?php $transtext_text_xml_value = ($text->transtext) ? ($text->transtext->text_xml) : NULL; ?>
            @include('widgets.form.formitem._textarea', 
                    ['name' => 'transtext.text_xml', 
                     'value' => $transtext_text_xml_value,
                     'title'=>trans('corpus.text_xml')])
        @endif
                 
        {{-- SOURCE --}}
        <?php $source_author_value = ($action=='edit' && $text->source) ? ($text->source->author) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'source.author', 
                 'value' => $source_author_value,
                 'title'=>trans('corpus.source_author')])
        <?php $source_title_value = ($action=='edit' && $text->source) ? ($text->source->title) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'source.title', 
                 'value' => $source_title_value,
                 'title'=>trans('corpus.source_title')])
        <div class="row">
            <div class="col-sm-6">
        <?php $source_year_value = ($action=='edit' && $text->source) ? ($text->source->year) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'source.year', 
                 'value' => $source_year_value,
                 'size' => 4,
                 'title'=>trans('corpus.source_year')])
            </div>
            <div class="col-sm-6">
        <?php $source_pages_value = ($action=='edit' && $text->source) ? ($text->source->pages) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'source.pages', 
                 'value' => $source_pages_value,
                 'title'=>trans('corpus.source_pages')])
            </div>
        </div>
        
        <b>{{ trans('corpus.archive_krc') }}</b>
        <?php $ieeh_archive_number1_value = ($action=='edit' && $text->source && $text->source->ieeh_archive_number1) ? ($text->source->ieeh_archive_number1) : NULL; ?>
        <?php $ieeh_archive_number2_value = ($action=='edit' && $text->source && $text->source->ieeh_archive_number2) ? ($text->source->ieeh_archive_number2) : NULL; ?>
        <div class="row">
            <div class="col-sm-1">
                <b>№</b> 
            </div>
            <div class="col-sm-5">
        @include('widgets.form.formitem._text', 
                ['name' => 'source.ieeh_archive_number1', 
                 'value' => $ieeh_archive_number1_value])
            </div>
            <div class="col-sm-1">
                <b>/</b>
            </div>
            <div class="col-sm-5">
        @include('widgets.form.formitem._text', 
                ['name' => 'source.ieeh_archive_number2', 
                 'value' => $ieeh_archive_number2_value])
            </div>
        </div>
        @include('widgets.form.formitem._text', 
                ['name' => 'youtube_id', 
                 'value' => ($action=='edit' && $text->video) ? ($text->video->youtube_id) : NULL,
                 'title'=>trans('corpus.youtube_id')])
        <?php $source_comment_value = ($action=='edit' && $text->source) ? ($text->source->comment) : NULL; ?>
        @include('widgets.form.formitem._textarea', 
                ['name' => 'source.comment', 
                 'value' => $source_comment_value,
                 'title'=>trans('corpus.comment'),
                 'attributes' => ['rows'=>3],
                ])
    </div>
</div>                 
                         


