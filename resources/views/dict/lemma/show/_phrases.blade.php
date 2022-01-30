@if (sizeof($phrases))
    <div>
        @if (sizeof($phrases)>3) 
        <a id="toggle-phrases" title="{{trans('messages.toggle_list')}}">
        @endif            
            <b>{{trans('dict.phrases')}}</b> 
        @if (sizeof($phrases)>3) 
        </a>
        @endif    
        
        @if (user_dict_edit())
        <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}{{$args_by_get?'&':'?'}}pos_id={{ 
                    \App\Models\Dict\PartOfSpeech::getPhraseID()}}&phrase_lemmas[{{$lemma->id}}]={{$lemma->lemma}}">
            <i class="fa fa-plus fa-lg add-phrase" title="{{trans('dict.add-phrase')}}"></i></a>
        @endif
            
        <div id="lemma-phrases" style="display:{{(sizeof($phrases)>3) ? 'none' : 'block'}}">
            @foreach ($phrases as $ph_lemma) 
            <a href="{{LaravelLocalization::localizeURL('/dict/lemma/'.$ph_lemma->id)}}">{{$ph_lemma->lemma}}</a> 
                - {{$ph_lemma->phraseMeaning()}}<br>
            @endforeach
        </div>
    </div>
@endif
