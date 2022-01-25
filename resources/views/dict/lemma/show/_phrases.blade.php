        @if (sizeof($lemma->phrases))
        <p>
            <b>{{trans('dict.phrases')}}:</b> 
            @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}{{$args_by_get?'&':'?'}}pos_id={{ 
                        \App\Models\Dict\PartOfSpeech::getPhraseID()}}&phrase_lemmas[{{$lemma->id}}]={{$lemma->lemma}}">
                <i class="fa fa-plus fa-lg add-phrase" title="{{trans('dict.add-phrase')}}"></i>
            </a>
            @endif
            
            @foreach ($lemma->phrases->sortBy('lemma') as $ph_lemma) 
            <br><a href="{{LaravelLocalization::localizeURL('/dict/lemma/'.$ph_lemma->id)}}">{{$ph_lemma->lemma}}</a> 
                - {{$ph_lemma->phraseMeaning()}}
            @endforeach

        </p>
        @endif
