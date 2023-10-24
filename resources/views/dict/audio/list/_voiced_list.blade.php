        @if (sizeof($audios))
        <table id="audiosTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('messages.updated_at') }}</th>
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audios as $audio)
                @foreach($audio->lemmas as $index=>$lemma)
            <tr id="row-{{$lemma->id}}">
                <td data-th="{{ trans('dict.lemmas') }}">
                    <a href="{{ LaravelLocalization::localizeURL('dict/lemma/'.$lemma->id) }}">
                        {{$lemma->lemma}}<br>
                    </a>
                </td>
                <td>
                    @foreach ($lemma->getMultilangMeaningTexts() as $meaning_string) 
                        {{$meaning_string}}<br>
                    @endforeach
                </td>
                <td data-th="{{ trans('messages.updated_at') }}" id="date-{{$lemma->id}}">
                    {{$audio->updated_at}}
                </td>
                <td data-th="{{ trans('messages.actions') }}">
            @if ($index==0)
                    <div id="audios-{{$lemma->id}}" class='audio-button' data-all-audios='0'>
                @include('widgets.audio_simple', ['route'=>$audio->url()])
                    </div>
                @include('widgets.form.button._delete', 
                         ['is_button'=>false, 
                          'without_text' => true,
                          'route' => 'informant.audio.destroy', 
                          'args'=>['id' => $audio->id, 'informant_id'=>$informant->id]])
                    <div class='record-button'>                  
                        <i id="record-audio-{{$lemma->id}}" 
                           class="fa fa-microphone record-audio record-stop fa-lg" 
                           data-id="{{$lemma->id}}"></i>
                    </div>
                    <div id="new-audio-{{$lemma->id}}" class="audio-player"></div>
            @endif
                </td>
            </tr>
                @endforeach
            @endforeach
        </tbody>
        </table>
        @endif
