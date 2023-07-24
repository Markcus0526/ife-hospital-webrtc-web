<script type="text/javascript">
$(function() {
	// init country name
	$('.td-country[country_id]').each(function() {
		country_id = $(this).attr('country_id');
		country_name = $('#country_id option[value="' + country_id + '"]').text();
		$(this).text(country_name);
	});

	$('#country_id').change(function() {
		$('#list_form').submit();
	})
	
	$('.btn-delete').click(function() {
		user_id = $(this).parents('tr').attr('user_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该患者？');?>', function() {
			App.callAPI("api/user/delete",
				{
					user_id: user_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除患者成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});
	
	$('.btn-unlock').click(function() {
		user_id = $(this).parents('tr').attr('user_id');
		
		confirmBox('<?php l('恢复');?>', '<?php l('是否确定恢复该账号?');?>', function() {
			App.callAPI("api/user/unlock",
				{
					user_id: user_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('恢复成功');?>', '<?php l('恢复账号成功。');?>', function() {
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