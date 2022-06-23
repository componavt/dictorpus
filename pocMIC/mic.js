let isRecordingInProgress = false,
	isRecordingComplete   = false,
	recordWindow = document.querySelector("[id='record']"),
	playerWindow = document.querySelector("[id='player']"),
	duration = 0,
	wordsURL = "words.json",
	wordsArray = [],
	currentWord = 0,
	url = "https://editportal.krc.karelia.ru/pocMIC/r.php",
	audioBlock = document.querySelector("#player");

fetch(wordsURL)
	.then(words => words.json())
	.then(words => parseWords(words.data));


function parseWords(data) {
	wordsArray = data;
	displayWord();
}

function displayWord() {
	audioBlock.innerHTML = wordsArray[currentWord].text;
}

document.addEventListener ("keydown", function (kEvent) {
	switch (kEvent.key)
	{
		case "w":
			startRecord();
			break;
		case "a":
			deleteRecord();
			break;
		case "s":
			playRecord();
			break;
		case "d":
			saveRecord();
			break;
		case "ц":
			startRecord();
			break;
		case "ф":
			deleteRecord();
			break;
		case "ы":
			playRecord();
			break;
		case "в":
			saveRecord();
			break;
	}
});

function startRecord()
{
	if (isRecordingInProgress) {
		console.log("Останавливаем запись");
		isRecordingInProgress = false;
		recordWindow.classList.toggle("inRecord");

		mediaRecorder.stop();

	} else {
		console.log("Начинаем записывать");
		isRecordingInProgress = true;
		recordWindow.classList.toggle("inRecord");


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
			location.reload();
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
			fd.append('text', wordsArray[currentWord].text);
			fd.append('audio', voiceBlob);
			oReq.send(fd);
			oReq.onload = function (oEvent) {

			};
			/**
			 * Вот это по идее должно быть в блоке выше
			 */
			isRecordingComplete = false;
			currentWord++;
			displayWord();
		} else {
			console.log("Записи еще нет");
		}

	}
}
