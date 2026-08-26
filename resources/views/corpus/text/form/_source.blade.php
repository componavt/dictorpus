{{-- SOURCE --}}
@php
    if ($action == 'create') {
        $publication_value = null;
        $pubpart_value = null;
    }

    $source = $action == 'edit' ? $text->source : null;

    $source_author_value = $source ? $source->author : null;
    $source_title_value = $source ? $source->title : null;
    $source_year_value = $source ? $source->year : null;
    $source_pages_value = $source ? $source->pages : null;

    $show_old_source_fields = $source && (
        trim((string) $source_author_value) !== '' ||
        trim((string) $source_title_value) !== '' ||
        trim((string) $source_year_value) !== ''
    );
@endphp        
        
@include('widgets.form.formitem._select2', 
        ['name' => 'source.publication_id', 
            'values' =>$publication_values,
            'value' => $publication_value,
            'is_multiple' => false,
            'call_add_onClick' => 'addPublication()',
            'call_add_title' => trans('messages.create_new_f'),
            'title' => trans('corpus.publication')]) 
@include('corpus.text.form._source_pubparts', [
    'source_pubparts' => $source ? $source->pubparts : collect()
]) 

@if ($show_old_source_fields)
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
@endif

<div class="row">
@if ($show_old_source_fields)
    <div class="col-sm-6">
<?php $source_year_value = ($action=='edit' && $text->source) ? ($text->source->year) : NULL; ?>
@include('widgets.form.formitem._text', 
        ['name' => 'source.year', 
            'value' => $source_year_value,
            'size' => 4,
            'title'=>trans('corpus.source_year')])
    </div>
@endif

    <div class="{{ $show_old_source_fields ? 'col-sm-6' : 'col-sm-12' }}">
        <?php $source_pages_value = ($action=='edit' && $text->source) ? ($text->source->pages) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'source.pages', 
                'value' => $source_pages_value,
                'title'=>trans('corpus.source_pages')])
    </div>
</div>
