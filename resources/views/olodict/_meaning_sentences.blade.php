<div  id="meaning-examples_{{$meaning->id}}">
    <h4>{{trans('messages.examples')}}</h4>
@foreach ($sentences as $sentence)
    <div class='meaning-sentence'>
            {{ $count++ }}.
            {!! $sentence['sent_obj']->markSearchWords([$sentence['w_id']] ?? [], $sentence['s'] ?? null); !!}

@if ($sentence['trans_s']) 
    <div class='meaning-sentence-trans'>{!! $sentence['trans_s'] !!}</div>
@endif
    </div>
@endforeach                    
</div>
