<audio id="player" controls></audio>
<script>
  const player = document.getElementById('player');

  const handleSuccess = function (stream) {
    if (window.URL) {
      player.srcObject = stream;
    } else {
      player.src = stream;
    }
  };

  navigator.mediaDevices
    .getUserMedia({audio: true, video: false})
    .then(handleSuccess);
</script>