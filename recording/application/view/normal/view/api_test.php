<h1>API调试网页</h1>

<form id="save_form" action="<?php p($this->api_url); ?>" class="form-horizontal" method="post" novalidate="novalidate">
	<div class="form-group">
		<label class="control-label col-sm-2" for="api_url">API URL</label>
		<div class="col-sm-10">
			<p class="form-control-static">
				<?php p($this->api_url); ?> 
			</p>
		</div>
	</div>
	<hr/>
	<?php foreach($this->api_param_names as $param_name) { ?>
	<div class="form-group">
		<label class="control-label col-sm-2" for="<?php p($param_name); ?>"><?php p($param_name); ?></label>
		<div class="col-sm-10">
			<?php $this->api_params->input($param_name); ?> 
		</div>
	</div>
	<?php } ?>
	<hr/>
	<div class="form-group">
		<label class="control-label col-sm-2" for="api_url">结果</label>
		<div class="col-sm-10">
			<pre id="api_result" class="form-control-static">
			</pre>
		</div>
	</div>

	<div class="navbar">
		<div class="navbar-inner">
			<div class="navbar-form pull-right">
				<button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-check"></i> 呼び出し</button>
				<a href="<?php p($this->apitest_url); ?>/../" class="btn btn-default"><i class="fa fa-fw fa-times"></i> 返回</a>
			</div>
		</div>
	</div>
</form>