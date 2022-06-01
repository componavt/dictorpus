<div style="display:flex; justify-content: space-between">
    <div>
<p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        
        @if ($lemma->pos)
        <p>
            <b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}  
            {{$lemma->featsToString()}}
        </p>
        @endif

        @if ($lemma->phoneticListWithDialectsToString())
        <p><b>{{ trans('dict.phonetics') }}:</b> {!! $lemma->phoneticListWithDialectsToString()!!}</p>
        @endif
        
        @if ($lemma->variantsWithLink())
        <p>
            <b>{{trans('dict.variants')}}:</b> {!!$lemma->variantsWithLink()!!}
        </p>
        @endif
        @if ($lemma->phraseLemmasListWithLink())
        <p>
            <b>{{trans('dict.phrase_lemmas')}}:</b> {!!$lemma->phraseLemmasListWithLink()!!}
        </p>
        @endif

        @include('dict.lemma.show._phrases')
        
        @if ($lemma->omonymsListWithLink())
        <p>
            <b{!! User::checkAccess('dict.edit')?' class="warning"':'' !!}>{{trans('dict.omonyms')}}:</b> {!!$lemma->omonymsListWithLink()!!}
        </p>
        @endif
    </div>
    <div style="text-align: right; font-style: italic">
        @include('dict.audio.record')
    </div>
</div>

