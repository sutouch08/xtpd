<?php $this->load->view('include/header'); ?>
<?php
$company = $tab == 'company' ? 'active in' : '';
$document = $tab == 'document' ? 'active in' : '';
$order = $tab == 'order' ? 'active in' : '';
$sap = $tab == 'SAP' ? 'active in' : '';
$system = $tab == 'system' ? 'active in' : '';
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr class="padding-5" />
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5 padding-right-0" style="padding-top:5px;">
		<ul id="myTab1" class="setting-tabs" style="margin-left:0px;">
			<li class="li-block <?php echo $company; ?>" onclick="changeURL('company')"><a href="#company" data-toggle="tab">ข้อมูลบริษัท</a></li>
			<li class="li-block <?php echo $document; ?>" onclick="changeURL('document')"><a href="#document" data-toggle="tab">เลขที่เอกสาร</a></li>
			<li class="li-block <?php echo $sap; ?>" onclick="changeURL('SAP')"><a href="#SAP" data-toggle="tab">ข้อมูล SAP</a></li>
			<li class="li-block <?php echo $system; ?>" onclick="changeURL('system')"><a href="#system" data-toggle="tab">ระบบ</a></li>
			<li class="li-block <?php echo $order; ?>" onclick="changeURL('order')"><a href="#order" data-toggle="tab">Order</a></li>
		</ul>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 col-xs-8 padding-5" style="padding-top:5px; border-left:solid 1px #ccc; min-height:600px; max-height:1500px;">
		<div class="tab-content" style="border:0px;">

			<!---  ตั้งค่าบริษัท  ------------------------------------------------------>
			<div class="tab-pane fade <?php echo $company; ?>" id="company">
				<?php $this->load->view('setting/setting_company'); ?>
			</div>
			<!---  ตั้งค่าเอกสาร  --------------------------------------------------->
			<div class="tab-pane fade <?php echo $document; ?>" id="document">
				<?php $this->load->view('setting/setting_document'); ?>
			</div>

			<div class="tab-pane fade <?php echo $order; ?>" id="order">
				<?php $this->load->view('setting/setting_order'); ?>
			</div>

			<div class="tab-pane fade <?php echo $sap; ?>" id="SAP">
				<?php $this->load->view('setting/setting_sap'); ?>
			</div>

			<div class="tab-pane fade <?php echo $system; ?>" id="system">
				<?php $this->load->view('setting/setting_system'); ?>
			</div>


		</div>
	</div><!--/ col-sm-9  -->
</div><!--/ row  -->


<script src="<?php echo base_url(); ?>scripts/setting/setting.js"></script>
<script src="<?php echo base_url(); ?>scripts/setting/setting_document.js"></script>
<?php $this->load->view('include/footer'); ?>