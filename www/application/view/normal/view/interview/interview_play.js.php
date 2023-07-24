<script type="text/javascript">
$(function() {
	var player = document.querySelector('#player');
<?php
if (RECORDING_API != '') {
?>
	$.ajax({
        url : "<?php p(RECORDING_API); ?>api/v/s?interview_id=<?php p($this->interview_id); ?>",
        type : "get",
        dataType : 'json',
        contentType: false,
        processData: false,
        success : function(data){
        	if (data.err_code == 0) {
        		switch(data.status) {
        			case 10:
        				url = "<?php p(RECORDING_API); ?>" + data.url;
        				player.src = url;
        				break;
        			case 2:
        				$('#error').text('<?php l("正在录像处理中，请耐心等候..."); ?>');
        				$(player).hide();
        				break;
        			case 1:
        				$('#error').text('<?php l("用户上传录像中..."); ?>');
        				$(player).hide();
        				break;
        			case 0:
        				$('#error').text('<?php l("对不起，该会诊不存在录像文件。"); ?>');
        				$(player).hide();
        				break;
        		}
        	}
            return data;
        },
        error : function() {
            return -1;
        }
    });
<?php
}
?>
});
</script>