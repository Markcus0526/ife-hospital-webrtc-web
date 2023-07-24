<script type="text/javascript">
var hasUserMedia = function() {
    return !!navigator.getUserMedia;
}
$(function() {
    var cstream = null,
        player_video = document.querySelector('#player_video'),
        player_speaker = document.querySelector('#player_speaker'),
        vdoW = 400,
        vdoH = 0;

    $('#check_camera').click(function() {
        if (!hasUserMedia()) {
            $('#video').addClass('video-check-error');
            return;
        }
        navigator.getUserMedia({
            video: true,
            audio: true
        }, function(stream) {
            cstream = stream;

            player_video.srcObject = stream;
            player_video.play();
            $('#video').removeClass('video-check-error');
        }, function(err) {
            console.log("An error occured! " + err);
            $('#video').addClass('video-check-error');
        });

        player_video.addEventListener('canplay', function(ev){
            vdoH = player_video.videoHeight / (player_video.videoWidth/vdoW);
            player_video.setAttribute('width', vdoW);
            player_video.setAttribute('height', vdoH);
        }, false);
    });

    var intM = null, 
        canM = document.querySelector('#mic_equalizer'), 
        canW = 300, canH = 30,
        ctxM = canM.getContext('2d'),
        acxM = new AudioContext(),
        srcM = null,
        anlM = null;

    $('#check_mic').click(function() {
        navigator.getUserMedia({
            video: true,
            audio: true
        }, function(stream) {
            if (intM) {
                clearInterval(intM);
                intM = null;
            }
            cstream = stream;
            srcM = acxM.createMediaStreamSource(stream),
            anlM = acxM.createAnalyser();

            srcM.connect(anlM);
            anlM.connect(acxM.destination);

            intM = setInterval(function(){
                var freqData = new Uint8Array(anlM.frequencyBinCount);

                anlM.getByteFrequencyData(freqData);

                ctxM.clearRect(0, 0, canW, canH);
                ctxM.fillStyle = "green";

                m = 0;
                for (var i = 0; i < freqData.length; i++ ) {
                    var magnitude = freqData[i];
                    ctxM.fillRect(i / anlM.frequencyBinCount * canW, canH, 
                        1, -(magnitude / 256 * canH));
                }
             }, 33);
        }, function(err) {
                console.log("An error occured! " + err);
        });
    });

    var intS = null, 
        canS = document.querySelector('#speaker_equalizer'), 
        ctxS = canS.getContext('2d'),
        acxS = new AudioContext(),
        srcS = acxS.createMediaElementSource(player_speaker),
        anlS = acxS.createAnalyser();

    srcS.connect(anlS);
    anlS.connect(acxS.destination);

    $('#check_speaker').click(function() {
        if (intS) {
            clearInterval(intS);
            intS = null;
        }

        player_speaker.play();

        intS = setInterval(function(){
            var freqData = new Uint8Array(anlS.frequencyBinCount);

            anlS.getByteFrequencyData(freqData);

            ctxS.clearRect(0, 0, canW, canH);
            ctxS.fillStyle = "green";

            m = 0;
            for (var i = 0; i < freqData.length; i++ ) {
                var magnitude = freqData[i];
                ctxS.fillRect(i / anlS.frequencyBinCount * canW, canH, 
                    1, -(magnitude / 256 * canH));
            }
         }, 33);
    });

    
});
</script>