<div  id="meaning-examples_{{$meaning->id}}">
@foreach ($sentences as $sentence)
    <div class='meaning-sentence'>
        @if (sizeof($sentences)>1)
            {{ $count++ }}.
        @else
            &nbsp;&nbsp;&nbsp;
        @endif
        {!! $sentence['sent_obj']->markSearchWords([$sentence['w_id']] ?? [], $sentence['s'] ?? null); !!}

@if ($sentence['trans_s']) 
    <div class='meaning-sentence-trans'>{!! $sentence['trans_s'] !!}</div>
@endif
    </div>
@endforeach                    
</div>
