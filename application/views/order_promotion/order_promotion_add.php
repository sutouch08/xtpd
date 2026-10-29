<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.autosize.js"></script>
<style>
	.form-group {
		margin-bottom: 5px;
	}
	.input-icon > .ace-icon {
		z-index: 1;
	}
</style>

<?php $hide = $this->disSale ? "" : 'hide'; ?>

<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<?php $this->load->view('order_promotion/order_promotion_add_header'); ?>
<?php $this->load->view('order_promotion/order_promotion_add_detail'); ?>
<?php $this->load->view('order_promotion/order_promotion_add_footer'); ?>

<input type="hidden" id="sale_id" value="<?php echo $this->_user->sale_id; ?>" />
<input type="hidden" id="default_currency" value="<?php echo getConfig('CURRENCY'); ?>" />
</form>


<!--  Add New Address Modal  --------->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:80%; min-width:400px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site" id="modal-title" style="margin-bottom:0px;" >ตรวจสอบข้อมูล</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" id="result">

                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-danger pull-left" onClick="dismiss('previewModal')" >Cancel</button>
								<button type="button" class="btn btn-md btn-success pull-right" id="btn-submit" disabled  onclick="saveAdd()">Submit</button>
            </div>
        </div>
    </div>
</div>


<script id="customer-template" type="text/x-handlebarsTemplate">
		<option value="">Select Customer</option>
		{{#each this}}
			<option value="{{CardCode}}" data-currency="{{Currency}}" data-sale="{{SlpCode}}" data-vat="{{ECVatGroup}}" data-rate="{{Rate}}">{{CardCode}}  {{CardName}}</option>
		{{/each}}
</script>

<script id="ship-to-template" type="text/x-handlebarsTemplate">
		{{#each this}}
			<option value="{{code}}">{{code}}</option>
		{{/each}}
</script>

<script id="bill-to-template" type="text/x-handlebarsTemplate">
		{{#each this}}
			<option value="{{code}}">{{code}}</option>
		{{/each}}
</script>

<script id="preview-template" type="text/x-handlebarsTemplate">
<table class="table table-bordered border-1" style="margin-bottom:10px;">
	<tr>
		<th class="width-30">
		รหัสลูกค้า
		<label class="pull-right">
		<input type="checkbox" class="ace check-list" onchange="toggleSubmit()">
		<span class="lbl"></span>
		</label>
		</th>
		<td>{{customerName}}</td>
	</tr>
	<tr><th>เลขที่ใบสั่งสินค้า</th><td>{{orderCode}}</td></tr>
	<tr><th>Promotion</th><td>{{promotionCode}} | {{promotionName}}</td></tr>
	<tr><th>ที่อยู่ตามใบกำกับภาษี</th><td>{{billToCode}}  {{billToAddress}}</td></tr>
	<tr><th>สถานที่ส่งของ</th><td>{{shipToCode}}  {{shipToAddress}}</td></tr>
	<tr><th>สถานที่จัดส่งเพิ่มเติม</th><td>{{exShipTo}}</td></tr>
	<tr><th>Currency</th><td>{{currency}} | Rate: {{currencyRate}} </td></tr>
	<tr><th>วันที่สั่งสินค้า</th><td>{{docDate}}</td></tr>
	<tr><th>วันที่จัดส่ง</th><td>{{dueDate}}</td></tr>
	<tr><th>เลขที่ PO</th><td>{{PoNo}}</td></tr>
	<tr><th>บิลลงวันที่</th><td>{{billOption}}</td></tr>
	<tr><th>ต้องการใบเสนอราคา</th><td>{{requiredSQ}}</td></tr>
	<tr><th>Remark สำหรับสื่อสารกับ Admin</th><td>{{remark}}</td></tr>
</table>
<table class="table table-bordered border-1" style="min-width:100%;">
	<thead>
		<tr>
			<th class="width-5 middle text-center">#</th>
			<th class="width-30 middle">รายการสินค้า</th>
			<th class="width-10 middle text-right">จำนวน</th>
			<th class="width-10 middle text-right">แถม</th>
			<th class="width-10 middle text-center">หน่วย</th>
			<th class="width-10 middle text-right">ราคา/หน่วย (Term)</th>
			<th class="width-10 middle text-right">ราคา(พิเศษ)/หน่วย</th>
			<th class="width-5 middle text-center <?php echo $hide; ?>">Discount Sales</th>
			<th class="width-10 middle text-right">มูลค่า</th>
			<th class=""></th>
		</tr>
	</thead>
	<tbody>
		{{#each items}}
			{{#if @last}}
				<tr>
					<td colspan="8" class="middle text-right"><strong>จำนวนเงินรวมทั้งสิ้น</strong></td>
					<td colspan="2" class="middle text-right">{{totalAmount}}</td>
				</tr>
			{{else}}
				<tr>
					<td class="middle text-center">{{no}}</td>
					<td class="middle">{{itemName}}</td>
					<td class="middle text-right">{{qty}}</td>
					<td class="middle text-right">{{free}}</td>
					<td class="middle text-center">{{uom}}</td>
					<td class="middle text-right">{{stdPrice}}</td>
					<td class="middle text-right">{{sellPrice}}</td>
					<td class="middle text-center <?php echo $hide; ?>">{{{dis}}}</td>
					<td class="middle text-right">{{amount}}</td>
					<td class="middle text-center">
						<label><input type="checkbox" class="ace check-list" onchange="toggleSubmit()"><span class="lbl"></span></label>
					</td>
				</tr>
			{{/if}}
		{{/each}}
	</tbody>
</table>
</script>






<script src="<?php echo base_url(); ?>scripts/order_promotion/order_promotion.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_promotion/order_promotion_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/address.js"></script>




<?php $this->load->view('include/footer'); ?>
