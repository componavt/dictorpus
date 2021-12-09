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
