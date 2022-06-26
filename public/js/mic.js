// https://webfanat.com/article_id/?id=150
let isRecordingInProgress = false,
	isRecordingComplete   = false,
	url = "/ru/dict/audio/upload",
        mediaRecorder,
        voiceBlob;
function recordAudio(lemma_id, informant_id, token) {
    $(".record-audio").on("click touchend", function(){
        startRecord(lemma_id, informant_id, url, token);
    });
}

function startRecord(lemma_id, informant_id, url, token)
{
    if (isRecordingInProgress) {
        console.log("Останавливаем запись...");
        isRecordingInProgress = false;
        $(".record-audio").removeClass('record-in-process');
        mediaRecorder.stop();
        
    } else {
        console.log("Начинаем записывать");
        isRecordingInProgress = true;
        $(".record-audio").addClass('record-in-process')

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
                                //audioBlock.append(recordedDuration);
                                this.ontimeupdate = null;
                            }
                            recordedAudio.currentTime = 0;
                        }
                    }
                });
                loadPlayer(voiceBlob);
                if (confirm("Записать аудио?")) {
                    saveRecord(voiceBlob, lemma_id, informant_id, url, token);
                }
            });

            mediaRecorder.addEventListener("dataavailable",function(event) {
                voice.push(event.data);
            });

            mediaRecorder.start();
        });
    }
}

function saveRecord(voiceBlob, lemma_id, informant_id, url, token) {
    console.log("Сохраняем запись");
    let fd = new FormData();
    fd.append('id', lemma_id);
    fd.append('informant_id', informant_id);
    fd.append('_token', token);
    fd.append('audio', voiceBlob);
    
    $.ajax({
        url: url, 
        data: fd,
        type: 'POST',
        processData: false,
        contentType: false,
        success: function(result){
//            $("#audios").append(result);
        }
    }); 
    
/*    var oReq = new XMLHttpRequest();
    oReq.open("POST", url, true);
    oReq.send(fd);
    oReq.onload = function (oEvent) {
    };*/
}
function loadPlayer(audioBlob) {
    const audioUrl = URL.createObjectURL(audioBlob);
    var audio = document.createElement('audio');
    audio.src = audioUrl;
    audio.controls = true;
    audio.autoplay = true;
    $('#new-audio').html(audio);
}
