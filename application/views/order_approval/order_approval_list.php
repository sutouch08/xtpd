<?php $this->load->view('include/header'); ?>

<style>
	.form-group {
		margin-bottom: 5px;
	}

	.input-icon>.ace-icon {
		z-index: 1;
	}

	.label.btn-block {
		padding: 3px 0px;
		font-size: 12px;
	}
</style>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"> <?php echo $this->title; ?></h4>
	</div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row f-row">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="label-sm">Web Order</label>
			<input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="label-sm">ลูกค้า</label>
			<input type="text" class="form-control input-sm text-center search-box" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="label-sm">เลขที่ PO</label>
			<input type="text" class="form-control input-sm text-center search-box" name="po" value="<?php echo $po; ?>" />
		</div>

		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label class="label-sm">User</label>
			<select class="form-control input-sm filter" name="user_id" id="user-id">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user_id($user_id); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="label-sm">Team condition</label>
			<select class="form-control input-sm filter" name="con_id">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sales_team_condition($con_id); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="label-sm">Discount Sales</label>
			<select class="form-control input-sm filter" name="is_discount_sales">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected("1", $is_discount_sales); ?>>มี</option>
				<option value="0" <?php echo is_selected('0', $is_discount_sales); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
			<label class="label-sm">Min. Amount</label>
			<select class="form-control input-sm filter" name="min_amount_filter">
				<option value="Y">Filtered</option>
				<option value="N" <?php echo is_selected('N', $min_amount_filter); ?>>All</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label class="label-sm">วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly />
				<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="label-sm display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="label-sm display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>

	<input type="hidden" name="search" value="1" />
</form>

<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped tableNarrow border-1" style="min-width:1060px; margin-bottom:0px;">
			<thead>
				<tr>
					<th class="fix-width-100">Actions</th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-80">วันที่</th>
					<th class="fix-width-100">Web Order</th>
					<th class="fix-width-100">User</th>
					<th class="fix-width-100">รหัสลูกค้า</th>
					<th class="min-width-200">ลูกค้า</th>
					<th class="fix-width-120">เลขที่ PO</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
					<th class="fix-width-120">STC.</th>
				</tr>
			</thead>
			<tbody>

				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach ($data as $rs) : ?>
						<tr id="row-<?php echo $rs->code; ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" title="preview" onclick="preview('<?php echo $rs->code; ?>')">Preview</button>
								<button type="button" class="btn btn-minier btn-primary" title="Authorizer" onclick="showAuthorize('<?php echo $rs->code; ?>')"><i class="fa fa-user"></i></button>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo thai_date($rs->date_add); ?></td>
							<td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $rs->uname; ?></td>
							<td class="middle"><?php echo $rs->CardCode; ?></td>
							<td class="middle"><?php echo $rs->CardName; ?></td>
							<td class="middle"><?php echo $rs->NumAtCard; ?></td>
							<td class="middle text-right"><?php echo number($rs->DocTotal, 2); ?></td>
							<td class="middle"><?php echo sales_team_condition_name($rs->Condition_id); ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="10" class="middle text-center">ไม่พบรายการ</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>



<!--  Add New Address Modal  --------->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:90%; min-width:400px; max-width:95vw; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title-site" id="modal-title" style="margin-bottom:0px;">Preview Order</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" id="result">

					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-default btn-100" id="btn-close" onClick="dismiss('previewModal')">Close</button>
				<button type="button" class="btn btn-white btn-danger btn-100 a-btn" id="btn-reject" onclick="doReject()" disabled>ไม่อนุมัติ</button>
				<button type="button" class="btn btn-white btn-success btn-100 a-btn" id="btn-approve" onclick="doApprove()" disabled>อนุมัติ</button>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="OrderCode" value="">


<script id="preview-template" type="text/x-handlebars-template">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
		<table class="table table-striped table-bordered tableNarrow border-1" style="margin-bottom:10px;">
			<tbody>
				<tr>
					<td class="fix-width-200">เลขที่ใบสั่งสินค้า</td>
					<td class="min-width-200">{{orderCode}}</td>
					<td class="fix-width-100">User</td>
					<td class="fix-width-200">{{user}}</td>
				</tr>
				<tr>
					<td>ลูกค้า</td>
					<td>{{customerName}}</td>
					<td>วันที่สั่งสินค้า</td>
					<td>{{docDate}}</td>
				</tr>
				<tr>
					<td>ที่อยู่ตามใบกำกับภาษี</td>
					<td>{{billToCode}} | {{billToAddress}}</td>
					<td>วันที่จัดส่ง</td>
					<td>{{dueDate}}</td>
				</tr>
				<tr>
					<td>สถานที่ส่งของ</td>
					<td>{{shipToCode}} | {{shipToAddress}}</td>
					<td>Currency</td>
					<td>{{currency}} | Rate: {{currencyRate}}</td>
				</tr>
				<tr>
					<td>สถานที่จัดส่งเพิ่มเติม</td>
					<td>{{exShipTo}}</td>
					<td>เลขที่ PO</td>
					<td>{{PoNo}}</td>
				</tr>
				<tr>
					<td>Price List</td>
					<td>{{PriceList}}</td>
					<td>Payment Terms</td>
					<td>{{termName}}</td>
				</tr>
				<tr>
					<td>ต้องการใบเสนอราคา</td>
					<td>{{requiredSQ}}</td>
					<td>บิลลงวันที่</td>
					<td>{{billOption}}</td>
				</tr>
				<tr>
					<td>Order Type</td>
					<td>{{isExport}}</td>
					<td>Promotion</td>
					<td>{{promotionCode}} {{promotionName}}</td>
				</tr>
				<tr>
					<td>Remark สำหรับสื่อสารกับ Admin</td>
					<td colspan="3">{{remark}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 padding-0 table-responsive" style="max-height:300px; overflow:auto;">
		<table class="table table-bordered tableNarrow" style="min-width:1350px; margin-bottom:0px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="min-width-250 middle text-center">รายการสินค้า</th>
					<th class="fix-width-80 middle text-center">จำนวน</th>
					<th class="fix-width-80 middle text-center">แถม</th>
					<th class="fix-width-80 middle text-center">หน่วย</th>
					<th class="fix-width-120 middle text-center">ราคา/หน่วย (Term)</th>
					<th class="fix-width-120 middle text-center">ราคา(พิเศษ)/หน่วย</th>
					<th class="fix-width-80 middle text-center">Disc. Sales</th>
					<th class="fix-width-100 middle text-center">มูลค่า</th>
					<th class="fix-width-100 middle text-center">หมายเหตุ</th>
					<th class="fix-width-100 middle text-center">จำนวนค้างส่ง</th>					
					<th class="fix-width-200 middle text-center">เหตุผลในการ Reject</th>
				</tr>
			</thead>
			<tbody>
				{{#each items}}
					<tr>
						<td class="middle text-center">{{{checkbox}}}</td>
						<td class="middle"><input type="text" class="form-control input-xs padding-0 text-label" value="{{itemName}}" readonly></td>
						<td class="middle text-right">{{qty}}</td>
						<td class="middle text-right">{{free}}</td>
						<td class="middle text-center">{{uom}}</td>
						<td class="middle text-right">{{stdPrice}}</td>
						<td class="middle text-right">{{sellPrice}}</td>
						<td class="middle text-center">{{{dis}}}</td>
						<td class="middle text-right">{{amount}}</td>
						<td class="middle"><input type="text" class="form-control input-xs padding-0 text-label" value="{{lineText}}" readonly></td>
						<td class="middle text-right">{{openQty}}</td>						
						<td class="middle"><input type="text" class="form-control input-xs padding-5 reject-box" id="reject-item-{{id}}" value="" ></td>
					</tr>
				{{/each}}
			</tbody>
		</table>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-top:20px; padding-right:7px;">&nbsp;</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-top:20px; padding-right:7px;">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-9 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ราคาสินค้า</label>
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.totalBefDi}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-9 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ส่วนลด [{{subTotal.DiscPrcnt}} %]</label>
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.DiscSum}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-9 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ราคาสุทธิก่อนภาษีมูลค่าเพิ่ม</label>
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.totalBefVat}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-9 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.totalVat}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-9 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">รวมเงินสุทธิ</label>
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.docTotal}}" readonly>
				</div>
			</div>
		</div>
	</div>	
</script>


<div class="modal fade" id="authorizer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:400px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title-site" id="modal-title" style="margin-bottom:0px;"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped table-bordered border-1">
							<thead>
								<tr>
									<th class="width-40">Username</th>
									<th class="width-60">Employee</th>
								</tr>
							</thead>
							<tbody id="authorizer-table">

							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-primary" onClick="dismiss('authorizer-modal')">Close</button>
			</div>
		</div>
	</div>
</div>

<script id="authorizer-template" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nodata}}
			<tr>
				<td colspan="2" class="text-center"> ---- No Authorizer ----</td>
			</tr>
		{{else}}
			<tr>
				<td>{{uname}}</td>
				<td>{{emp_name}}
			</tr>
		{{/if}}
	{{/each}}
</script>

<script>
	$('#user-id').select2();
	$(document).ready(function() {
		setTimeout(function() {
			window.location.reload();
		}, 1000 * 60 * 10); //--- reload every 10 minutes
	});
</script>

<script src="<?php echo base_url(); ?>scripts/order_approval/order_approval.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>