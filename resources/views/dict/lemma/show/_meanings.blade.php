        @foreach ($lemma->meanings as $meaning)
        <div class="lemma-meaning">
            <div class="lemma-meaning-left">
                <h3>{{$meaning->meaning_n}}  {{ trans('dict.meaning') }}</h3>

                @include('dict.lemma.show.meaning.concepts')

                @include('dict.lemma.show.meaning.texts')

                @include('dict.lemma.show.meaning.relations')

                @include('dict.lemma.show.meaning.translations')

                @if ($meaning->dialectListToString())
                <p><b>{{ trans('dict.dialects_usage') }}:</b> {{ $meaning->dialectListToString()}}</p>
                @endif
                    
            </div>
            <div class="lemma-meaning-examples">
                <img class="img-loading" id="img-loading_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">
                <div  id="meaning-examples_{{$meaning->id}}"></div>
            </div>
            </tr>
        </div>
        @endforeach
