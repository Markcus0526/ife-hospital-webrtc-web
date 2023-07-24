<script type="text/javascript">
$(function() {
    var streaming = false,
        video        = document.querySelector('#video'),
        canvas       = document.querySelector('#canvas'),
        width = 320,
        height = 0;

    navigator.mediaDevices.getUserMedia = ( navigator.mediaDevices.getUserMedia ||
        navigator.mediaDevices.webkitGetUserMedia ||
        navigator.mediaDevices.mozGetUserMedia ||
        navigator.mediaDevices.msGetUserMedia);

    navigator.mediaDevices.getUserMedia(
        {
            video: true,
            audio: false
        }
    )
    .then(
        function(stream) {
            if (navigator.mozGetUserMedia) {
                video.mozSrcObject = stream;
            } else {
                var vendorURL = window.URL || window.webkitURL;
                video.src = vendorURL.createObjectURL(stream);
            }
            video.play();
            $('#btn_capture').removeAttr('disabled');
        }
    )
    .catch(
        function(err) {
            console.log("An error occured! " + err);
            $('#media_error').text("<?php l("请安装摄像头。"); ?>");
            $('#btn_capture').attr('disabled', 'disabled');
        }
    );

    video.addEventListener('canplay', function(ev){
        if (!streaming) {
            height = video.videoHeight / (video.videoWidth/width);
            video.setAttribute('width', width);
            video.setAttribute('height', height);
            canvas.setAttribute('width', width);
            canvas.setAttribute('height', height);
            streaming = true;
        }
    }, false);

    $('#btn_ok')
        .attr('disabled', 'disabled')
        .click(function() {
            parent.onBoothComplete($('#photo').attr('src'));
            parent.$.fancybox.close();
        });

    $('#btn_close')
        .click(function() {
            parent.$.fancybox.close();
        });

    $('#btn_capture')
        .click(function(ev){
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(video, 0, 0, width, height);
            var png_data = canvas.toDataURL('image/png');
            
            png_data = png_data.replace(/^data:image\/(png|jpg);base64,/, "");

            App.callAPI('api/common/booth', {
                png_data: png_data,
                mode: '<?php p($this->mode); ?>'
            })
            .done(function(res) {
                $('#photo').attr('src', res.tmp_path);
                $('#btn_ok').removeAttr('disabled');
            })
            .fail(function(res) {
            });
        });
});
</script>