<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_select', 
                ['name' => 'corpus_id', 
                 'values' =>$corpus_values,
                 'title' => trans('corpus.corpus')]) 
                 
    </div>
    <div class="col-sm-4">
        @include('widgets.form._formitem_select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'title' => trans('navigation.dialects'),
                 'class'=>'multiple-select-dialect form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form._formitem_select2',
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
        @include('widgets.form._formitem_select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lang_id']])
        @include('widgets.form._formitem_text', 
                ['name' => 'title', 
                 'title'=>trans('corpus.title')])
        
        <?php $attr = ['id'=>'text']; ?>
        @if ($text->meanings()->wherePivot('relevance','<>',1)->count())
            <?php $attr[] = 'readonly'; ?>
            <p class="warning text-has-checked-meaning">
                {{trans('corpus.text_has_checked_meaning')}}
                <button type="button" class="btn btn-info text-unlock">
                    {{trans('corpus.unlock')}}
                </button>

            </p>
        @endif
        @include('widgets.form._formitem_textarea', 
                ['name' => 'text', 
                 'title'=>trans('corpus.text'),
                 'attributes' => $attr,
                ])
        @if ($action == 'edit')
            @include('widgets.form._formitem_textarea', 
                    ['name' => 'text_xml', 
                     'title'=>trans('corpus.text_xml')])
        @endif
        
        {{-- EVENT --}}
        <?php $informant_id_value = ($action=='edit' && $text->event) ? ($text->event->informant_id) : NULL; ?>
        @include('widgets.form._formitem_select', 
                ['name' => 'event.informant_id', 
                 'values' =>$informant_values,
                 'value' => $informant_id_value,
                 'title' => trans('corpus.informant')]) 
        <?php $event_date_value = ($action=='edit' && $text->event) ? ($text->event->date) : NULL; ?>
        @include('widgets.form._formitem_text', 
                ['name' => 'event.date', 
                 'value' => $event_date_value,
                 'size' => 4,
                 'title'=>trans('corpus.record_year')])
        <?php $event_place_value = ($action=='edit' && $text->event) ? ($text->event->place_id) : NULL; ?>
        @include('widgets.form._formitem_select', 
                ['name' => 'event.place_id', 
                 'values' =>$place_values,
                 'value' => $event_place_value,
                 'title' => trans('corpus.record_place')]) 
        <?php if ($action=='create') { $recorder_value = NULL; } ?>        
        @include('widgets.form._formitem_select2',
                ['name' => 'event.recorders', 
                 'values' =>$recorder_values,
                 'value' => $recorder_value,
                 'title' => trans('corpus.recorded'),
                 'class'=>'multiple-select form-control'
            ])
                
@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
    </div>
    <div class="col-sm-6">
        <?php $transtext_lang_id_value = ($action=='edit' && $text->transtext) ? ($text->transtext->lang_id) : NULL; ?>
        @include('widgets.form._formitem_select', 
                ['name' => 'transtext.lang_id', 
                 'values' =>$lang_values,
                 'value' => $transtext_lang_id_value,
                 'title' => trans('corpus.transtext_lang')]) 
        <?php $transtext_title_value = ($action=='edit' && $text->transtext) ? ($text->transtext->title) : NULL; ?>
        @include('widgets.form._formitem_text', 
                ['name' => 'transtext.title', 
                 'value' => $transtext_title_value,
                 'title'=>trans('corpus.transtext_title')])
        <?php $transtext_text_value = ($action=='edit' && $text->transtext) ? ($text->transtext->text) : NULL; ?>
        @include('widgets.form._formitem_textarea', 
                ['name' => 'transtext.text', 
                 'value' => $transtext_text_value,
                 'title'=>trans('corpus.transtext_text')])
        @if ($action=='edit')
            <?php $transtext_text_xml_value = ($text->transtext) ? ($text->transtext->text_xml) : NULL; ?>
            @include('widgets.form._formitem_textarea', 
                    ['name' => 'transtext.text_xml', 
                     'value' => $transtext_text_xml_value,
                     'title'=>trans('corpus.text_xml')])
        @endif
                 
        {{-- SOURCE --}}
        <?php $source_author_value = ($action=='edit' && $text->source) ? ($text->source->author) : NULL; ?>
        @include('widgets.form._formitem_text', 
                ['name' => 'source.author', 
                 'value' => $source_author_value,
                 'title'=>trans('corpus.source_author')])
        <?php $source_title_value = ($action=='edit' && $text->source) ? ($text->source->title) : NULL; ?>
        @include('widgets.form._formitem_text', 
                ['name' => 'source.title', 
                 'value' => $source_title_value,
                 'title'=>trans('corpus.source_title')])
        <div class="row">
            <div class="col-sm-6">
        <?php $source_year_value = ($action=='edit' && $text->source) ? ($text->source->year) : NULL; ?>
        @include('widgets.form._formitem_text', 
                ['name' => 'source.year', 
                 'value' => $source_year_value,
                 'size' => 4,
                 'title'=>trans('corpus.source_year')])
            </div>
            <div class="col-sm-6">
        <?php $source_pages_value = ($action=='edit' && $text->source) ? ($text->source->pages) : NULL; ?>
        @include('widgets.form._formitem_text', 
                ['name' => 'source.pages', 
                 'value' => $source_pages_value,
                 'attributes'=>['size' => 15],
                 'title'=>trans('corpus.source_pages')])
            </div>
        </div>
        
        <?php $ieeh_archive_number1_value = ($action=='edit' && $text->source && $text->source->ieeh_archive_number1) ? ($text->source->ieeh_archive_number1) : NULL; ?>
        <?php $ieeh_archive_number2_value = ($action=='edit' && $text->source && $text->source->ieeh_archive_number2) ? ($text->source->ieeh_archive_number2) : NULL; ?>
        <div class="row">
            <div class="col-sm-8">
        @include('widgets.form._formitem_text', 
                ['name' => 'source.ieeh_archive_number1', 
                 'value' => $ieeh_archive_number1_value,
                 'attributes'=>['size' => 5],
                 'title' => trans('corpus.archive_krc') .': № '])
            </div>
            <div class="col-sm-1">
                <b>/</b>
            </div>
            <div class="col-sm-3">
        @include('widgets.form._formitem_text', 
                ['name' => 'source.ieeh_archive_number2', 
                 'value' => $ieeh_archive_number2_value,
                 'attributes'=>['size' => 5]])
            </div>
        </div>
        <?php $source_comment_value = ($action=='edit' && $text->source) ? ($text->source->comment) : NULL; ?>
        @include('widgets.form._formitem_textarea', 
                ['name' => 'source.comment', 
                 'value' => $source_comment_value,
                 'title'=>trans('corpus.comment'),
                 'attributes' => ['rows'=>3],
                ])
    </div>
</div>                 
                         


