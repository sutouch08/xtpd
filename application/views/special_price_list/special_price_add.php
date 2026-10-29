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

<hr class="padding-5" />

<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Price List Name</label>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm e" id="name" maxlength="100" autofocus placeholder="Enter price list name - Required" />
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-offset-3 col-xs-12" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Type</label>
		<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-12">
			<select class="form-control input-sm e" id="type">
				<option value="">Select type</option>
				<?php echo select_price_list_type(); ?>
			</select>
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-offset-3 col-xs-12" id="type-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Start Date</label>
		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
			<input type="datetime-local" class="form-control input-sm min-width-150 e" id="start-date" value="<?php echo date('Y-m-d\T00:00'); ?>" />
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-offset-3 col-xs-12" id="start-date-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">End Date</label>
		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
			<input type="datetime-local" class="form-control input-sm min-width-150 e" id="end-date" value="<?php echo date('Y-m-d\T23:59'); ?>" />
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-offset-3 col-xs-12" id="end-date-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace e" name="status" value="1" />
				<span class="lbl"> Active</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace e" name="status" value="0" checked />
				<span class="lbl"> Inactive</span>
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-offset-3 col-xs-12" id="status-error"></div>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="form-group">
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">&nbsp;</div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<?php if ($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success btn-100 btn-xs-block" onclick="add()"><i class="fa fa-plus"></i> Add</button>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
	window.addEventListener('load', function() {
		bindDateTimeRange('#start-date', '#end-date');
	});
</script>
<script src="<?php echo base_url(); ?>scripts/special_price_list/special_price_list.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>