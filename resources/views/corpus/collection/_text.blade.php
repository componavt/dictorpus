<p>
   <a class="subdiv-title" href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{$text->title}}</a>

@if ($event && $event->place)
<br>@include('corpus.place._to_string',
            ['place' => $event->place])@if($event->date), 
            {{ $event->date }} @endif
    @endif

@if ($source)    
<?php
    $source_info = [];
    if ($source->bookToString()) {
        $source_info[] = $source->bookToString();
    }
    
    if ($source->ieeh_archive_number1) {
        $archive = '№'.$source->ieeh_archive_number1;
        
        if ($source->ieeh_archive_number2) {
            $archive .= '/'.$source->ieeh_archive_number2;
        }
        
        $source_info[] = $archive;
    }
    
    if ($source->comment) {
        $source_info[] = process_text($source->comment);
    }
?>
    @if (sizeof($source_info)) 
    <br>{!!join('<br>', $source_info)!!}
    @endif
@endif
</p>
