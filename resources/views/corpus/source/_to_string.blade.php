<?php

    $source_info = [];
    if ($source->bookToString()) {
        $source_info[] = $source->bookToString();
    }
    
    if ($source->ieeh_archive_number1) {
        $archive = '<i>'.\Lang::get('corpus.archive_krc').':</i> №'.$source->ieeh_archive_number1;
        
        if ($source->ieeh_archive_number2) {
            $archive .= '/'.$source->ieeh_archive_number2;
        }
        
        $source_info[] = $archive;
    }
 
    if ($source->comment) {
        $source_info[] = $source->comment;
    }
    
    $source_str = join('<br>',$source_info);
?>
@if ($source_str)
    @if(!isset($without_tag))
    <i>{{ trans('corpus.source') }}:</i> 
    @endif
    {!! $source_str !!}
@endif