	<form id="documentForm" method="post" action="<?php echo $this->home; ?>/update_config">
		<div class="row">
			<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-8 margin-bottom-10">
				<span class="form-control left-label text-right">Prefix Order</span>
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 margin-bottom-10">
				<input type="text" class="width-100 text-center prefix" name="PREFIX_SALES_ORDER" required value="<?php echo $PREFIX_ORDER; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-8 padding-5 margin-bottom-10">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 margin-bottom-10">
				<input type="number" class="width-100 text-center digit" required name="RUN_DIGIT_SALES_ORDER" value="<?php echo $RUN_DIGIT_ORDER; ?>" />
			</div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-lg-6 col-md-8-harf col-sm-10 col-xs-12 padding-5">
				<?php if ($this->pm->can_edit or $this->_SuperAdmin) : ?>
					<button type="button" class="btn btn-sm btn-success input-small pull-right" onClick="checkDocumentSetting()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
			</div>
			<div class="divider-hidden"></div>

		</div><!--/ row -->
	</form>