<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner">
    	<div class="row">
    		<div class="col-xs-6">
		        <?php $thisyear = date("Y"); ?>
		        <?php if ($thisyear > 2017) p("2017 - "); p(date("Y")); ?> © 3QC全球远程医疗会诊系统 <?php p(VERSION); ?>
	        </div>
	        <div class="col-xs-6 text-right">
	        	<?php if(strlen(CONTACT_TEL) > 0) { ?>
	        	管理者電話：<i class="fa fa-phone"></i> <?php p(CONTACT_TEL); ?>
	        	<?php } ?>
	        </div>
        </div>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
