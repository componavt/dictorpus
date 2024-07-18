@if (!empty($sentence))
    @if (isset($with_left_context) && $with_left_context) 
<?php
        $left_context = \App\Models\Corpus\Sentence::whereTextId($sentence->text_id)
                                ->whereSId(($sentence->s_id) - 1)->first();
?>
        @if ($left_context)
        <span id='context_{{$left_context->id}}'>
            <i class="load-context fa fa-arrow-left" onClick="callContextSentence({{$left_context->id}}, 'left')" title="{{trans('search.add_left_context')}}"></i>
        </span>
        @endif
    @endif
    
    {!! $sentence->addWordBlocks($marked_words ?? [], $sentence_xml ?? null); !!}

    @if (isset($with_right_context) && $with_right_context) 
<?php
        $right_context = \App\Models\Corpus\Sentence::whereTextId($sentence->text_id)
                                ->whereSId(($sentence->s_id) + 1)->first();
?>
        @if ($right_context)
        <span id='context_{{$right_context->id}}'>
            <i class="load-context fa fa-arrow-right" onClick="callContextSentence({{$right_context->id}}, 'right')" title="{{trans('search.add_right_context')}}"></i>
        </span>
        @endif
    @endif
@endif
