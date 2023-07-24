<script type="text/javascript">
$(function() {
	$('.btn-delete').click(function() {
		chistory_id = $(this).parents('tr').attr('chistory_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该病历？');?>', function() {
			App.callAPI("api/chistory/delete",
				{
					chistory_id: chistory_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除病历成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});
});
</script>