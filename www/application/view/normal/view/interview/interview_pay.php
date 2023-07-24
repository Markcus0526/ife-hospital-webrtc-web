 <section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<?php l("支付页面");?>
			</li>
		</ul>
	</div>
	<div id="pay_form" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="form-group static">
				<label class="control-label col-md-4"><?php l("会诊编号"); ?> :</label>
				<div class="col-md-8">
					<p class="form-control-static"><?php p($mInterview->interview_id); ?></p>
				</div>
			</div>
			<div class="form-group static">
				<label class="control-label col-md-4"><?php l("支付金额"); ?> :</label>
				<div class="col-md-8">
					<p class="form-control-static">
						<?php $mInterview->currency("cost", "cunit"); ?>
						&nbsp;&nbsp;&nbsp;
						<?php $mInterview->currency("ex_cost", "ex_cunit"); ?>
					</p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4"><?php l("电子合同"); ?> :</label>
				<div class="col-md-8">
					<div class="checkbox-list">
						<label class="ui-checkbox" for="agree"> 
							<input type="checkbox" id="agree" name="agree" value="1" class="">
							<span>
								<?php l("已同意签订"); ?><a href="javascript:;" id="contract_link" class="active"><?php l("《3QC国际医疗服务合同》"); ?></a>
							</span>
						</label>
					</div>
				</div>
			</div>
			<div id="div_pay_buttons" class="form-group static hide">
				<label class="control-label col-md-4"><?php l("支付方式"); ?> :</label>
				<div class="col-md-8">
					<?php p($this->chinapay_button); ?>
					<?php p($this->paypal_button); ?>
					<p class="pay-warning">
						<span><?php l("*注意");?></span>：
						<span>
							<?php l("使用银联在线支付人民币价格"); ?><br/>
							<?php l("使用PayPal支付美元价格"); ?><br/>
							<?php l("中国PayPal账户无法使用PayPal支付方式进行支付"); ?>
						</span>
					</p>
				</div>
			</div>
		</div>
	</div>
	<div id="waiting_payment_dialog" class="modal fade opacity8" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php l("网上支付提示"); ?></h4>
				</div>
				<div class="modal-body">
					<span class="content">
						<div><span class="text-danger">*</span><?php l("支付完成前，请不要关闭此支付验证窗口。"); ?></div>
						<div><span class="text-danger">*</span><?php l("支付完成后，请根据您支付的情况点击下面按钮。");?></div>
					</span>
				</div>
				<div class="modal-footer text-center">
					<a id="error_payment" class="btn btn-primary"><?php l("支付遇到问题"); ?></a>
					<a id="complete_payment" class="btn btn-primary"><?php l("支付完成"); ?></a>
				</div>
			</div>
		</div>
	</div>

	 <div id="contract_view" class="modal fade">
		 <div class="modal-dialog">
			 <div class="modal-content">
				 <div class="modal-header">
					 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					 <h4 class="modal-title"><?php l("《3QC国际医疗服务合同》"); ?></h4>
				 </div>
				 <div class="modal-body contract-container">
					 <?php $this->mInterview->detail_html("contract"); ?>
				 </div>
				 <div class="modal-footer">
					 <button type="button" class="btn btn-default btn-close-cancel-form" data-dismiss="modal"><?php l("确认"); ?></button>
				 </div>
			 </div>
		 </div>
	 </div>
</section>