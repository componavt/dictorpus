@extends('layouts.page')

@section('page_title')
Запись аудио {{$list_title}}
@stop

@section('headExtra')
    {!!Html::style('css/mic.css')!!}
@stop

@section('body')
<div class="row">
    <div class="col-sm-2" style="text-align: right">{{trans('corpus.record_by')}}</div>
    <div class="col-sm-10">
        @include('widgets.form.formitem._select', 
                ['name' => 'informant_id', 
                 'values' =>$informant_values,
                 'value' => $informant_id]) 
    </div>
</div>
	<div id="record">
            <div class='hor_flex'>
		<div id="prev"></div>
		<div id="next"></div>                
            </div>
		<div id="recordTimer">Идет запись!</div>
		<div id="player"></div>
		<div id="meanings"></div>
		<div id="audio"></div>
		<ul id="legend">
			<li>W - Начать/остановить запись</li>
			<li>A - Удалить запись</li>
			<li>S - Прослушать запись</li>
			<li>D - Сохранить запись</li>
			<li>N - Следующее слово</li>
		</ul>
	</div>
@stop

@section('jqueryFunc')
    let isRecordingInProgress = false,
	isRecordingComplete   = false,
	duration = 0,
	currentWord = 0,
	url = "/ru/dict/audio/upload",
	recordWindow = document.querySelector("[id='record']"),
	playerWindow = document.querySelector("[id='player']"),
	meaningsWindow = document.querySelector("[id='meanings']"),
	prevWindow = document.querySelector("[id='prev']"),
	nextWindow = document.querySelector("[id='next']"),
	audioBlock = document.querySelector("#audio");
	wordsArray = [	
        @foreach ($lemmas as $lemma)
        {"id": {{$lemma->id}}, 
         "text": "{{$lemma->lemma}}", 
         "meanings": "{!!join('<br>', preg_replace('/"/', '\"', \App\Models\Dict\Lemma::meaningTextsForId($lemma->id)))!!}"},
        @endforeach
        ];
/* console.log(wordsArray.length); */
	displayWord();
        displayNext();
        
document.addEventListener ("keydown", function (kEvent) {
	switch (kEvent.key)
	{
		case "w":
		case "ц":
			startRecord();
			break;
		case "a":
		case "ф":
			deleteRecord();
			break;
		case "s":
		case "ы":
			playRecord();
			break;
		case "d":
		case "в":
			saveRecord();
			break;
		case "n":
		case "т":
			nextWord();
			break;
	}
});

function displayWord() {
	playerWindow.innerHTML = wordsArray[currentWord].text;
	meaningsWindow.innerHTML = wordsArray[currentWord].meanings;
}

function displayNext() {
    if (wordsArray.length > 1) {
        nextWindow.innerHTML = wordsArray[1+currentWord].text;
    } else {
        nextWindow.innerHTML = '';
    }
}

function startRecord()
{
	if (isRecordingInProgress) {
		console.log("Останавливаем запись...");
		isRecordingInProgress = false;
		recordWindow.classList.toggle("inRecord");

		mediaRecorder.stop();

	} else {
		console.log("Начинаем записывать");
		isRecordingInProgress = true;
		recordWindow.classList.toggle("inRecord");
		playerWindow.classList.toggle("inRecord");


		if (audioBlock !== undefined) {
			while (audioBlock.hasChildNodes()) {
				audioBlock.removeChild(audioBlock.firstChild);
			}
		}

		navigator.mediaDevices.getUserMedia({ audio: true})
			.then(stream => {
				mediaRecorder = new MediaRecorder(stream)
				let voice = [];

				mediaRecorder.addEventListener("stop", function() {
					voiceBlob = new Blob(voice, {
						type: 'audio/wav'
					});
					const audioUrl = URL.createObjectURL(voiceBlob);
					recordedAudio = new Audio(audioUrl);
					recordedAudio.name = "player";
					recordedAudio.controls = false;

					isRecordingComplete = true;

					recordedAudio.addEventListener('loadedmetadata', function () {
						if (recordedAudio.duration === Infinity) {
							recordedAudio.currentTime = 1e101;
							recordedAudio.ontimeupdate = function () {
								this.ontimeupdate = () => {
									console.log("Продолжительность "+ recordedAudio.duration);
									console.log("Звук " + (voiceBlob.size / 1024).toFixed(3) + " КБ");
									duration = recordedAudio.duration;

									let recordedDuration = document.createElement("div");
									recordedDuration.innerHTML = "Записано "+duration+"сек."
									audioBlock.append(recordedDuration);
									this.ontimeupdate = null;
								}
								recordedAudio.currentTime = 0;
							}
						}
					});

				});

				mediaRecorder.addEventListener("dataavailable",function(event) {
					voice.push(event.data);
				});

				mediaRecorder.start();
			});
	}
}
function playRecord()
{
	if (isRecordingInProgress) {
		console.warn("Мы еще пишем");
	} else {
		if (isRecordingComplete) {
			console.log("Играем аудио");
			recordedAudio.play();
		} else {
			console.log("Записи еще нет");
		}
	}

}
function deleteRecord()
{
	if (isRecordingInProgress) {
		console.warn("Мы еще пишем");
	} else {
		if (isRecordingComplete) {
			console.log("Удаляем запись");
//			location.reload();
		} else {
			console.log("Записи еще нет");
		}
	}

}
function saveRecord()
{
	if (isRecordingInProgress) {
		console.warn("Мы еще пишем");
	} else {
		if (isRecordingComplete) {
			console.log("Сохраняем запись");
			var oReq = new XMLHttpRequest();
			oReq.open("POST", url, true);
			let fd = new FormData();
			fd.append('id', wordsArray[currentWord].id);
/*			fd.append('text', wordsArray[currentWord].text);*/
			fd.append('informant_id', $("#informant_id").val());
			fd.append('_token', '{{ csrf_token() }}');
			fd.append('audio', voiceBlob);
/*alert($("#informant_id").val());*/
                        
			oReq.send(fd);
			oReq.onload = function (oEvent) {
			};

                        prevWindow.innerHTML = '<a href="/dict/lemma/'+wordsArray[currentWord].id+'">'+wordsArray[currentWord].text + "</a> записано.";
			isRecordingComplete = false;
			currentWord++;
			displayWord();
                        displayNext();
		} else {
			console.log("Записи еще нет");
		}

	}
}

function nextWord() {
    currentWord++;
    isRecordingInProgress = false;
    isRecordingComplete = false;
    displayWord();
    displayNext();
}

@stop
