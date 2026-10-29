<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><i class="fa fa-user-circle"></i>&nbsp; <?php echo $this->title; ?></h4>
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
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Username</label>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="width-100 e" value="<?php echo $data->uname; ?>" disabled />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Employee</label>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="width-100 e" value="<?php echo $data->emp_name; ?>" disabled />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Maximum Approval Amount</label>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
			<input type="number" name="amount" id="amount" class="form-control input-sm e" value="<?php echo $data->amount; ?>" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="amount-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace" name="status" value="1" <?php echo is_checked($data->status, 1); ?> />
				<span class="lbl">&nbsp; Active</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace" name="status" value="0" <?php echo is_checked($data->status, 0); ?> />
				<span class="lbl">&nbsp; Disactive</span>
			</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Can Approve (A1)</label>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace" name="can_approve" value="1" <?php echo is_checked($data->can_approve, 1); ?> />
				<span class="lbl">&nbsp; Yes</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace" name="can_approve" value="0" <?php echo is_checked($data->can_approve, 0); ?> />
				<span class="lbl">&nbsp; No</span>
			</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Can Review (A2)</label>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace" name="can_review" value="1" <?php echo is_checked($data->can_review, 1); ?> />
				<span class="lbl">&nbsp; Yes</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace" name="can_review" value="0" <?php echo is_checked($data->can_review, 0); ?> />
				<span class="lbl">&nbsp; No</span>
			</label>
		</div>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<?php if ($this->pm->can_edit) : ?>
		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
				<button type="button" class="btn btn-white btn-success btn-100 btn-xs-block" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
			</div>
		</div>

		<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>" />
	<?php endif; ?>
</form>

<script src="<?php echo base_url(); ?>scripts/credit_approver/credit_approver.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>