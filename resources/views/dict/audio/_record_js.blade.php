    let isRecordingInProgress = false,
	isRecordingComplete   = false,
	duration = 0,
	currentWord = 0,
        wordsTotal = {{sizeof($lemmas)}},
	url = "/ru/dict/audio/upload",
        informant_id = $("#informant_id").val(),
	recordWindow = document.querySelector("[id='record']"),
	playerWindow = document.querySelector("[id='player']"),
	meaningsWindow = document.querySelector("[id='meanings']"),
	prevWindow = document.querySelector("[id='prev']"),
	nextWindow = document.querySelector("[id='next']"),
	numberWindow = document.querySelector("[id='number']"),
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
		case "q":
		case "й":
			deleteWord();
	}
});

function displayWord() {
	playerWindow.innerHTML = wordsArray[currentWord].text;
	meaningsWindow.innerHTML = wordsArray[currentWord].meanings;
	numberWindow.innerHTML = 1 + currentWord + ' из ' + wordsTotal;
}

function displayNext() {
    if (currentWord < wordsTotal - 1) {
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

function deleteWord()
{
    console.log("Удаляем слово из списка");
    var oReq = new XMLHttpRequest();
    oReq.open("GET", '/dict/audio/list/'+informant_id+'/delete/'+wordsArray[currentWord].id, true);
    let fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');

    oReq.send(fd);
    oReq.onload = function (oEvent) {
    };

    prevWindow.innerHTML = '<a href="/dict/lemma/'+wordsArray[currentWord].id+'">'+wordsArray[currentWord].text + "</a> удалено из списка.";
    currentWord++;
    displayWord();
    displayNext();
}

function nextWord() {
    currentWord++;
    isRecordingInProgress = false;
    isRecordingComplete = false;
    displayWord();
    displayNext();
}

