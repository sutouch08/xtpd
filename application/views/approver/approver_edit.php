<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->

<hr class="padding-5 margin-bottom-30" />

<form class="form-horizontal">
	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Username</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="width-100 e" value="<?php echo $data->uname; ?>" disabled />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Employee</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="width-100 e" value="<?php echo $data->emp_name; ?>" disabled />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Min. Amount</label>
		<div class="col-lg-1-harf col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="min-amount" class="form-control input-sm text-right e" value="<?php echo number_format($data->min_amount, 2); ?>" autocomplete="off" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="min-amount-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Max. Amount</label>
		<div class="col-lg-1-harf col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="max-amount" class="form-control input-sm text-right e" value="<?php echo number_format($data->amount, 2); ?>" autocomplete="off" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="max-amount-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace" name="status" value="1" <?php echo $data->status == 1 ? 'checked' : ''; ?> />
				<span class="lbl">&nbsp; &nbsp;Active</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace" name="status" value="0" <?php echo $data->status == 0 ? 'checked' : ''; ?> />
				<span class="lbl">&nbsp; &nbsp;Inactive</span>
			</label>
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<?php if ($this->pm->can_edit) : ?>
		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
				<button type="button" class="btn btn-sm btn-success btn-100 btn-xs-block" id="btn-save" onclick="update()"><i class="fa fa-save"></i>&nbsp;&nbsp; Update</button>
			</div>
		</div>
	<?php endif; ?>
		<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/approver/approver.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>