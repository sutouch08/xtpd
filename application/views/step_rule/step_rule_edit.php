<?php $this->load->view('include/header'); ?>
<style>
	.tableFixHead thead th {
		outline: 0;
	}

	.fix-header {
		outline: 0;
	}
</style>
<?php
$tab = isset($tab) ? $tab : 'info';
$info = $tab === 'info' ? 'active in' : '';
$items = $tab === 'items' ? 'active in' : '';
?>

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
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 padding-5 padding-right-0" style="padding-top:5px;">
		<ul id="myTab1" class="setting-tabs" style="margin-left:0px;">
			<li class="li-block <?php echo $info; ?>" onclick="changeURL(<?php echo $doc->PriceList; ?>, 'info')"><a href="#info" data-toggle="tab">Price List</a></li>
			<li class="li-block <?php echo $items; ?>" onclick="changeURL(<?php echo $doc->PriceList; ?>, 'items')"><a href="#items" data-toggle="tab">Step</a></li>
		</ul>
	</div>

	<div class="col-lg-10-harf col-md-10 col-sm-10 col-xs-12 padding-5" style="padding-top:5px; border-left:solid 1px #ccc;max-height:1500px;">
		<div class="tab-content" style="border:0px;">
			<div class="tab-pane fade <?php echo $info; ?>" id="info">
				<div class="form-horizontal">
					<div class="form-group margin-top-30">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Price List</label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<input type="text" class="form-control input-sm" id="price-list-name" value="<?php echo price_list_name($doc->PriceList); ?>" disabled />
							<input type="hidden" id="price-list" value="<?php echo $doc->PriceList; ?>" />
						</div>
						<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" id="price-list-error"></div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Description</label>
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
							<input type="text" class="form-control input-sm e" id="name" placeholder="Description" value="<?php echo $doc->name; ?>" autocomplete="off" />
						</div>
						<div class="error-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" id="name-error"></div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-top-7">
							<label class="fix-width-100">
								<input type="radio" class="ace" name="active" value="1" <?php echo $doc->active == 1 ? 'checked' : ''; ?> />
								<span class="lbl"> Active</span>
							</label>
							<label class="fix-width-100">
								<input type="radio" class="ace" name="active" value="0" <?php echo $doc->active == 0 ? 'checked' : ''; ?> />
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
							<button type="button" class="btn btn-white btn-success btn-100 btn-xs-block" onclick="update()"><i class="fa fa-save"></i> Save</button>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- tab-pane -->
			<div class="tab-pane fade <?php echo $items; ?>" id="items">
				<?php $this->load->view('step_rule/step_detail'); ?>
			</div><!-- tab-pane -->
		</div><!-- End Tab Content -->
	</div><!-- End Col -->
</div><!-- End Row -->

<script src="<?php echo base_url(); ?>scripts/step_rule/step_rule.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>