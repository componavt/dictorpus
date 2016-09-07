<?php
    $source_info = [];
    $book = [];
    
    if ($source->author) {
        $book[] = $source->author;
    }
    
    if ($source->title) {
        $book[] = $source->title;
    }
    
    if ($source->year) {
        $book[] = '('.$source->year.')';
    }
    
    if ($source->pages) {
        $book[] = \Lang::get('corpus.p').' '.$source->pages;
    }
    
    if (sizeof($book)) {
        $source_info[] = join(', ', $book);
    }
    
    if ($source->ieeh_archive_number1) {
        $archive = '<i>'.\Lang::get('corpus.archive_krc').':</i> №'.$source->ieeh_archive_number1;
        
        if ($source->ieeh_archive_number2) {
            $archive .= '/'.$source->ieeh_archive_number2;
        }
        
        $source_info[] = $archive;
    }
    
    
    $source_str = join('<br>',$source_info);
?>
@if ($source_str)
    <i>{{ trans('corpus.source') }}:</i> {!! $source_str !!}
@endif