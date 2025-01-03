<?php

    $source_info = [];
    if ($source->bookToString()) {
        $source_info[] = $source->bookToString();
    }
    
    if ($source->ieeh_archive_number1) {
        $archive = '<b>'.\Lang::get('corpus.archive_krc').':</b> <i>№'.$source->ieeh_archive_number1;
        
        if ($source->ieeh_archive_number2) {
            $archive .= '/'.$source->ieeh_archive_number2;
        }
        
        $source_info[] = $archive.'</i>';
    }
 
    if ($source->comment) {
        $source_info[] = '<i>'.process_text($source->comment). '</i>';
    }
    
    $source_str = join('<br>',$source_info);
?>
@if ($source_str)
    @if(!isset($without_tag))
    <div class="metadata-title">{{ trans('corpus.source') }}:</div> 
    @endif
    <i>{!! $source_str !!}</i>
@endif