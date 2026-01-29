<div style="display:flex; justify-content: space-between">
    <div>
        @if (empty($lemma->is_norm))
        <p>
            <b>{{ trans('dict.is_norm_values')[0] }}</b>
        </p>
        @endif

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
    <div class="lemma-audio-b">
        <div id="audios-{{$lemma->id}}" data-all-audios='1'>
        @include('dict.audio.view_audios')
        </div>
        @if (User::checkAccess('dict.edit'))
        <div style="text-align: right; padding-left:20px;">
            <i id="record-audio-{{$lemma->id}}" class="fa fa-microphone record-audio record-stop" data-id="{{$lemma->id}}"></i>
            <div id="new-audio-{{$lemma->id}}" class='audio-player' style="margin-top:20px;"></div>
        </div>
        @endif
    </div>
</div>

