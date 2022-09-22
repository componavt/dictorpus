<h2>{{$lemma->lemma}}</h2>

@if ($lemma->pos)
<p>
    <b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}  
    {{$lemma->featsToString()}}
</p>
@endif

        <div id="audios">
        @include('dict.audio.view_audios')
        </div>


        @if ($lemma->phraseLemmasListWithLink())
        <p>
            <b>{{trans('dict.phrase_lemmas')}}:</b> {!!$lemma->phraseLemmasListWithLink()!!}
        </p>
        @endif
        
@if (sizeof($phrases))
    <div>
        <b>{{trans('dict.phrases')}}</b> 
        
        <div id="lemma-phrases">
            @foreach ($phrases as $ph_lemma) 
            <a href="{{LaravelLocalization::localizeURL('/dict/lemma/'.$ph_lemma->id)}}">{{$ph_lemma->lemma}}</a> 
                - {{$ph_lemma->phraseMeaning()}}<br>
            @endforeach
        </div>
    </div>
@endif
