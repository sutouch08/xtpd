<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><i class="fa fa-users"></i> <?php echo $this->title; ?></h4>
  </div>
	<div class="col-md-6 col-sm-6 col-xs-4 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>

<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Group Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $data->name; ?>" autofocus required />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
		<label class="col-sm-3 col-xs-6 control-label no-padding-right hidden-xs"></label>
		<div class="col-lg-3 col-md-2 col-sm-2 col-xs-6"></div>
    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6">
			<button type="button" class="btn btn-sm btn-success btn-block" id="btn-save" onclick="update()"><i class="fa fa-save"></i>&nbsp;&nbsp Update</button>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>">
</form>

<script src="<?php echo base_url(); ?>scripts/user_group/user_group.js"></script>
<?php $this->load->view('include/footer'); ?>
