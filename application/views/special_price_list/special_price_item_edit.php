<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>	
</div><!-- End Row -->

<hr class="padding-5" />

<div class="row">
	<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<label>Item Code</label>
		<input type="text" class="width-100" id="item-code" value="<?php echo $item->ItemCode; ?>" disabled />
		<input type="hidden" id="id" value="<?php echo $item->id; ?>" />
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 padding-5">
		<label>Item Name</label>
		<input type="text" class="width-100" id="item-name" value="<?php echo $item->ItemName; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Uom</label>
		<input type="text" class="width-100" id="uom" value="<?php echo $item->UomCode; ?>" disabled />
	</div>	
</div>

<hr class="padding-5 margin-top-15 margin-bottom-15">

<?php $this->load->view('special_price_list/special_price_detail'); ?>

<div class="divider"></div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<button type="button" class="btn btn-white btn-default btn-50" onclick="closeTab()">Cancel</button>
		<button type="button" class="btn btn-white btn-success btn-50" onclick="saveItem()"><i class="fa fa-save"></i> &nbsp;Save</button>
	</div>
</div>
<script>
	function closeTab() {
		window.close();
	}
</script>
<script src="<?php echo base_url(); ?>scripts/special_price_list/special_price_list.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>