<?php $this->load->view('include/header'); ?>
<style>
	.form-group {
		margin-bottom: 5px;
	}
</style>
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
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Price List</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select class="form-control input-sm e" id="price-list">
				<option value="">Select</option>
				<?php echo select_price_list(); ?>
			</select>
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" id="price-list-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Description</label>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
			<input type="text" class="form-control input-sm e" id="name" placeholder="Description" autocomplete="off" />
		</div>
		<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace" name="active" value="1" checked />
				<span class="lbl"> Active</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace" name="active" value="0" />
				<span class="lbl"> Inactive</span>
			</label>
		</div>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>	

	<div class="form-group"></div>
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">&nbsp;</label>
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
			<?php if ($this->pm->can_add) : ?>
				<button type="button" class="btn btn-white btn-success btn-100 btn-xs-block" onclick="add()"><i class="fa fa-plus"></i> Add</button>
			<?php endif; ?>
		</div>
</div>

<script>
	$('#price-list').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/step_rule/step_rule.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>