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
