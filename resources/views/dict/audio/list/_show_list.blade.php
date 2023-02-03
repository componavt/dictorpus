        @if (sizeof($audios))
        <table id="audiosTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audios as $audio)
                @foreach($audio->lemmas as $index=>$lemma)
            <tr>
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
                <td data-th="{{ trans('messages.actions') }}">
            @if ($index==0)
                    <div class='audio-button'>
                @include('widgets.audio_simple', ['route'=>$audio->url()])
                    </div>
                @include('widgets.form.button._delete', 
                         ['is_button'=>false, 
                          'without_text' => true,
                          'route' => 'audio.destroy', 
                          'args'=>['id' => $audio->id]])
                <i class="fa fa-microphone record-audio record-stop fa-lg" 
                   onClick="startRecord({{$lemma->id}}, {{$informant->id}}, '/ru/dict/audio/upload', '{{ csrf_token() }}')"></i>
            @endif
                </td>
            </tr>
                @endforeach
            @endforeach
        </tbody>
        </table>
        @endif
