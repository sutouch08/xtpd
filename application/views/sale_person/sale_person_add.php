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
<hr class="padding-5 margin-bottom-30"/>

<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Sale Person Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="width-100" maxlength="50" value="" autofocus required />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="divider-hidden"></div>

  <div class="form-group">
    <label class="col-sm-3 col-xs-6 control-label no-padding-right hidden-xs"></label>
		<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6"></div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
			<button type="button" class="btn btn-sm btn-success btn-block" id="btn-save" onclick="saveAdd()"><i class="fa fa-save"></i>&nbsp;&nbsp Add</button>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>

	<input type="hidden" name="xx">
	<input type="text" name"xxx" class="hide">
</form>

<script src="<?php echo base_url(); ?>scripts/sale_person/sale_person.js"></script>
<?php $this->load->view('include/footer'); ?>
