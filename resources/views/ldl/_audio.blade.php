        @include('widgets.audio_decor', ['route'=>$audio->url()])
        
        <div style="position: relative">
            <i id='audio-{{$audio->id}}' class="fa fa-info-circle audio-info-caller" aria-hidden="true"></i>
            <div id='info-audio-{{$audio->id}}' class='audio-info'>
                <b>{{trans('dict.speaker')}}</b><br> 
                @if ($audio->informant)
                <big>{{$audio->informant->name}}</big><br>
                    @if ($audio->informant->birth_date)
                    {{$audio->informant->birth_date}} {{ trans('corpus.birth_year') }}<br>
                    @endif
                    @if ($audio->informant->birth_place)
                    {{ trans('corpus.birth_place') }}: {{$audio->informant->birthPlaceString('',false)}} 
                    @endif
                @endif
            </div>
        </div>
