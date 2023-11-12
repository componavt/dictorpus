@if ($sentence_count)
    <h4>{{ trans('messages.examples')}} 
        ({{ $sentence_count}})
    </h4>
    <p>
        @foreach (trans('dict.relevance_scope_example') as $r_k=> $r_v) 
        <span class='relevance relevance-{{$r_k}}'>
            <span class="glyphicon glyphicon-star"></span> 
            {{$r_v}}
        </span>
        @endforeach
    </p>

    @include('dict.lemma.example._limit', ['is_edit'=>0])

    <div id="more-{{$meaning->id}}" class="more-examples"></div>   

    <img class="img-loading" id="img-loading-more_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">

    <a id="show-more-{{$meaning->id}}" class="show-more-examples" style="display: none"
       onClick ="showExamples({{ $meaning->id }})">
            {{ trans('dict.more_examples') }}
    </a>

    <a id="hide-more-{{$meaning->id}}" class="hide-more-examples" style="display: none"
       onClick ="hideExamples({{$meaning->id}})">
            {{ trans('dict.hide_examples') }}
    </a>
                            
@endif
