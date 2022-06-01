@foreach ($lemma->audios as $audio)
        @include('widgets.audio_simple', ['route'=>$audio->url()])
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
        </div>
        </div>
@endforeach
<br>
<!--a id="download">Download</a>
<button id="stop">Stop</button>
<script>
  const downloadLink = document.getElementById('download');
  const stopButton = document.getElementById('stop');


  const handleSuccess = function(stream) {
    const options = {mimeType: 'audio/webm'};
    const recordedChunks = [];
    const mediaRecorder = new MediaRecorder(stream, options);

    mediaRecorder.addEventListener('dataavailable', function(e) {
      if (e.data.size > 0) recordedChunks.push(e.data);
    });

    mediaRecorder.addEventListener('stop', function() {
      downloadLink.href = URL.createObjectURL(new Blob(recordedChunks));
      downloadLink.download = 'acetest.wav';
    });

    stopButton.addEventListener('click', function() {
      mediaRecorder.stop();
    });

    mediaRecorder.start();
  };

  navigator.mediaDevices.getUserMedia({ audio: true, video: false })
      .then(handleSuccess);
</script-->