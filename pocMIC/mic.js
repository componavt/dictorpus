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
		case "�":
			startRecord();
			break;
		case "�":
			deleteRecord();
			break;
		case "�":
			playRecord();
			break;
		case "�":
			saveRecord();
			break;
	}
});

function startRecord()
{
	if (isRecordingInProgress) {
		console.log("������������� ������");
		isRecordingInProgress = false;
		recordWindow.classList.toggle("inRecord");

		mediaRecorder.stop();

	} else {
		console.log("�������� ����������");
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
									console.log("����������������� "+ recordedAudio.duration);
									console.log("���� " + (voiceBlob.size / 1024).toFixed(3) + " ��");
									duration = recordedAudio.duration;

									let recordedDuration = document.createElement("div");
									recordedDuration.innerHTML = "�������� "+duration+"���."
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
		console.warn("�� ��� �����");
	} else {
		if (isRecordingComplete) {
			console.log("������ �����");
			recordedAudio.play();
		} else {
			console.log("������ ��� ���");
		}
	}

}
function deleteRecord()
{
	if (isRecordingInProgress) {
		console.warn("�� ��� �����");
	} else {
		if (isRecordingComplete) {
			console.log("������� ������");
			location.reload();
		} else {
			console.log("������ ��� ���");
		}
	}

}
function saveRecord()
{
	if (isRecordingInProgress) {
		console.warn("�� ��� �����");
	} else {
		if (isRecordingComplete) {
			console.log("��������� ������");
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
			 * ��� ��� �� ���� ������ ���� � ����� ����
			 */
			isRecordingComplete = false;
			currentWord++;
			displayWord();
		} else {
			console.log("������ ��� ���");
		}

	}
}
