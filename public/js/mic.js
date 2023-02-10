// https://webfanat.com/article_id/?id=150
let isRecordingInProgress = false,
	isRecordingComplete   = false,
	url = "/ru/dict/audio/upload",
        mediaRecorder,
        voiceBlob;
function recordAudio(informant_id, token) {
    $(".record-audio").on("click touchend", function(e){
        let lemma_id=$(this).data('id');
        e.preventDefault();
        startRecord(lemma_id, informant_id, token);
    });
}

function startRecord(lemma_id, informant_id, token)
{
    if (isRecordingInProgress) {
        console.log("Останавливаем запись...");
        isRecordingInProgress = false;
        $("#record-audio-"+lemma_id).removeClass('record-in-process');
        mediaRecorder.stop();
        
    } else {
        console.log("Начинаем записывать");
        isRecordingInProgress = true;
        $("#record-audio-"+lemma_id).addClass('record-in-process')

        navigator.mediaDevices.getUserMedia({ audio: true})
                .then(stream => {
            mediaRecorder = new MediaRecorder(stream)
            let voice = [];

            mediaRecorder.addEventListener("stop", function() {
                voiceBlob = new Blob(voice, {
                    type: 'audio/wav'
                });
                const audioUrl = URL.createObjectURL(voiceBlob);
                let recordedAudio = new Audio(audioUrl);
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
//                loadPlayer(voiceBlob, lemma_id);
//                if (confirm("Записать аудио?")) {
                    saveRecord(voiceBlob, lemma_id, informant_id, token);
//                }
            });

            mediaRecorder.addEventListener("dataavailable",function(event) {
                voice.push(event.data);
            });

            mediaRecorder.start();
        });
    }
}

function saveRecord(voiceBlob, lemma_id, informant_id, token) {
    let allAudios=$("#audios-"+lemma_id).data('all-audios');
    console.log("Сохраняем запись");
    console.log("lemma_id:"+lemma_id+", informant_id:"+informant_id+", allAudios:"+allAudios);
    let fd = new FormData();
    fd.append('id', lemma_id);
    fd.append('informant_id', informant_id);
    fd.append('_token', token);
    fd.append('audio', voiceBlob);
    fd.append('all_audios', allAudios);
    
    $.ajax({
        url: url, 
        data: fd,
        type: 'POST',
        processData: false,
        contentType: false,
        success: function(result){
            $("#audios-"+lemma_id).html(result);
//            $("#audios-"+lemma_id).play();
            $("#date-"+lemma_id).html($('#update-'+lemma_id).val());
            $("#row-"+lemma_id).css('background','rgb(207, 243, 193)');
        }
    }); 
    
/*    var oReq = new XMLHttpRequest();
    oReq.open("POST", url, true);
    oReq.send(fd);
    oReq.onload = function (oEvent) {
    };*/
}
function loadPlayer(audioBlob, lemma_id) {
    const audioUrl = URL.createObjectURL(audioBlob);
    var audio = document.createElement('audio');
    audio.src = audioUrl;
    audio.controls = true;
    audio.autoplay = true;
    var c = document.createAttribute("class");
    c.value = "simple";
    audio.setAttributeNode(c);
    $('#new-audio-'+lemma_id).html(audio);
}
