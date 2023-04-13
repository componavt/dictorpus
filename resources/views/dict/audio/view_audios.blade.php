@foreach ($lemma->audios as $audio)
        @include('widgets.audio_simple', ['route'=>$audio->url(), 'autoplay'=>$audio->informant_id == $informant_id])
        <div style="position: relative">
            <i id='audio-{{$audio->id}}' class="fa fa-info-circle fa-lg audio-info-caller" aria-hidden="true"></i>
            <div id='info-audio-{{$audio->id}}' class='audio-info'>
                <b>{{trans('dict.speaker')}}</b><br> 
                @if ($audio->informant)
                <big>{{$audio->informant->name}}</big><br>
                    @if ($audio->informant->birth_date)
                    {{$audio->informant->birth_date}} г.р.<br>
                    @endif
                    @if ($audio->informant->birth_place)
                    место рождения: {{$audio->informant->birthPlaceString('',false)}} 
                    @endif
                @endif
                <div style='text-align: right'>
                @if (User::checkAccess('dict.edit'))
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/dict/audio/'.$audio->id.'/edit'])
                @endif     
                </div>
            </div>
        </div>
@endforeach
<br>
