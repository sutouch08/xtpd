<?php $this->load->view('include/header'); ?>

<style>
	h4.status-label {
		width: 150px;
		height: 50px;
		float: right;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	h4.status-label.red {
		background-color: #fdd9d9;
		color: #a94442;
		border: 1px solid #a94442;
	}

	h4.status-label.orange {
		background-color: #fcf8e3;
		color: #8a6d3b;
		border: 1px solid #8a6d3b;
	}

	h4.status-label.green {
		background-color: #dff0d8;
		color: #3c763d;
		border: 1px solid #3c763d;
	}

	.search-label {
		font-size: 12px;
		margin-bottom: 0px;
		margin-top: 8px;
	}

	.table>tbody>tr>td {
		padding: 5px !important;
		font-size: 12px !important;
	}

	.table>thead>tr>th {
		padding: 5px !important;
		text-align: center;
		font-size: 12px !important;
	}

	.table-bordered>thead>tr>th,
	.table-bordered>tbody>tr>td {
		border: 1px solid #bababa;
	}

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

	.sap-label {
		padding-top: 7px;
		border-bottom: 1px solid #ddd;
	}

	.credit-issue {
		background-color: #fdd9d9;
	}

	@media (min-width: 768px) {

		.fix-no {
			left: 0px;
			position: sticky;
		}

		.fix-date {
			left: 40px;
			position: sticky;
		}

		.fix-code {
			left: 170px;
			position: sticky;
		}

		.fix-user {
			left: 270px;
			position: sticky;
		}

		.fix-cust {
			left: 370px;
			position: sticky;
		}

		td[scope=row] {
			background-color: #ffffff;
			/* border-top:solid 1px #dddddd; */
			border: 0 !important;
			outline: solid 1px #bababa;
			z-index: 2;
		}

		tr.credit-issue>td[scope=row] {
			background-color: #fdd9d9;
		}
	}
</style>

<?php $hide = $this->disSale ? "" : 'hide'; ?>

<div class="row h-row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5" style="padding-top:5px;">
		<h3 class="title"> <?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if ($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i> New Order</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5" style="margin-bottom:0px;" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row f-row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">เลขที่</label>
			<input type="text" class="form-control input-sm text-center search-box" name="WebCode" value="<?php echo $WebCode; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">ลูกค้า</label>
			<input type="text" class="form-control input-sm text-center search-box" name="CardCode" value="<?php echo $CardCode; ?>" placeholder="Code OR Name" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">SO No.</label>
			<input type="text" class="form-control input-sm text-center search-box" name="DocNum" value="<?php echo $DocNum; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">DO No.</label>
			<input type="text" class="form-control input-sm text-center search-box" name="DeliveryNo" value="<?php echo $DeliveryNo; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">Invoice No.</label>
			<input type="text" class="form-control input-sm text-center search-box" name="InvoiceNo" value="<?php echo $InvoiceNo; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">เลขที่ PO</label>
			<input type="text" class="form-control input-sm text-center search-box" name="PoNo" value="<?php echo $PoNo; ?>" />
		</div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label class="search-label">User</label>
			<select class="form-control input-sm filter" name="user_id" id="user-id">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user_id($user_id); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label class="search-label">การอนุมัติ</label>
			<select class="form-control input-sm filter" name="Approved">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $Approved); ?>>รออนุมัติ</option>
				<option value="A" <?php echo is_selected('A', $Approved); ?>>อนุมัติ</option>
				<option value="AP" <?php echo is_selected('AP', $Approved); ?>>อนุมัติบางส่วน</option>
				<option value="R" <?php echo is_selected('R', $Approved); ?>>ไม่อนุมัติ</option>
			</select>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label class="search-label">ผู้อนุมัติ</label>
			<select class="form-control input-sm filter" name="Approver" id="approver">
				<option value="all">ทั้งหมด</option>
				<?php echo select_approver_uname($Approver); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label class="search-label">Credit Issue</label>
			<select class="form-control input-sm filter" name="credit_issue">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $credit_issue); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $credit_issue); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label class="search-label">Overdue</label>
			<select class="form-control input-sm filter" name="is_over_due">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_over_due); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_over_due); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="search-label">Team condition</label>
			<select class="form-control input-sm" name="con_id" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sales_team_condition($con_id); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="search-label">สถานะ</label>
			<select class="form-control input-sm" name="Status" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $Status); ?>>Not Export</option>
				<option value="1" <?php echo is_selected('1', $Status); ?>>Pending</option>
				<option value="2" <?php echo is_selected('2', $Status); ?>>Success</option>
				<option value="3" <?php echo is_selected('3', $Status); ?>>Error</option>
				<option value="-1" <?php echo is_selected('-1', $Status); ?>>Cancelled</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="search-label">Order Type</label>
			<select class="form-control input-sm" name="is_export" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_export); ?>>Export</option>
				<option value="0" <?php echo is_selected('0', $is_export); ?>>Local</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="search-label">SO Status</label>
			<select class="form-control input-sm" name="SO_Status" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="x" <?php echo is_selected("x", $SO_Status); ?>>No SO</option>
				<option value="O" <?php echo is_selected('O', $SO_Status); ?>>Open</option>
				<option value="C" <?php echo is_selected('C', $SO_Status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $SO_Status); ?>>Cancelled</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="search-label">DO Status</label>
			<select class="form-control input-sm" name="DO_Status" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="x" <?php echo is_selected("x", $DO_Status); ?>>No DO</option>
				<option value="P" <?php echo is_selected('P', $DO_Status); ?>>Partial</option>
				<option value="F" <?php echo is_selected('F', $DO_Status); ?>>Full</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="search-label">Invoice Status</label>
			<select class="form-control input-sm" name="INV_Status" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="x" <?php echo is_selected("x", $INV_Status); ?>>No Invoice</option>
				<option value="P" <?php echo is_selected('P', $INV_Status); ?>>Partial</option>
				<option value="F" <?php echo is_selected('F', $INV_Status); ?>>Full</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="search-label">Discount Sales</label>
			<select class="form-control input-sm" name="is_discount_sales" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected("1", $is_discount_sales); ?>>มี</option>
				<option value="0" <?php echo is_selected('0', $is_discount_sales); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label class="search-label">วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly />
				<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
			</div>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>

	<input type="hidden" name="search" value="1" />
</form>

<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1 table-responsive" id="item-div" style="overflow: auto; padding-left:0px; padding-right:0px; padding-bottom:5px; margin-left:5px; margin-right:5px;">
		<table class="table table-bordered tableFixHead border-1" style="margin-left: -1px; margin-top: -1px; min-width:1870px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center fix-no fix-header">#</th>
					<th class="fix-width-130 middle fix-date fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-code fix-header">เลขที่ WebOrder</th>
					<th class="fix-width-100 middle fix-user fix-header">User</th>
					<th class="fix-width-90 middle text-center fix-cust fix-header">รหัสลูกค้า</th>
					<th class="min-width-250 middle">ลูกค้า</th>
					<th class="fix-width-120 middle">เลขที่ PO</th>
					<th class="fix-width-100 middle text-right">มูลค่า</th>
					<th class="fix-width-60 middle text-center">Credit Issue</th>
					<th class="fix-width-60 middle text-center">Overdue</th>
					<th class="fix-width-70 middle text-center">Preview</th>
					<th class="fix-width-90 middle text-center">สถานะ</th>
					<th class="fix-width-80 middle text-center">ผู้มีสิทธิ์อนุมัติ</th>
					<th class="fix-width-80 middle text-center">การอนุมัติ</th>
					<th class="fix-width-100 middle">ผู้อนุมัติ</th>
					<th class="fix-width-80 middle text-center">เลขที่ SO (SAP)</th>
					<th class="fix-width-80 middle text-center">SO Status</th>
					<th class="fix-width-80 middle text-center">DO Status</th>
					<th class="fix-width-80 middle text-center">Invoice Status</th>
					<th class="fix-width-80 middle text-center">STC.</th>
				</tr>
			</thead>
			<tbody>
				<?php if (! empty($data)) : ?>
					<?php $no = $this->uri->segment(3) + 1; ?>
					<?php $users = users_array(); //--- user_helper 
					?>
					<?php foreach ($data as $rs) : ?>
						<?php $credit_issue = ($rs->credit_issue == 1 && $rs->credit_approval != 'A') ? 1 : 0; ?>
						<tr>
							<td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
							<td class="middle text-center fix-date" scope="row">
								<?php echo thai_date($rs->date_add, TRUE, '/'); ?>
							</td>
							<td class="middle fix-code" scope="row"><?php echo $rs->code; ?></td>
							<td class="middle fix-user" scope="row"><?php echo $rs->uname; ?></td>
							<td class="middle text-center fix-cust" scope="row"><?php echo $rs->CardCode; ?></td>
							<td class="middle"><?php echo $rs->CardName; ?></td>
							<td class="middle">
								<?php if ($rs->has_file) : ?>
									<!-- <span class="label label-info label-white middle width-100 pointer" style="text-align: left;" onclick="openFile('<?php echo $rs->file_name; ?>')">
									</span> -->
									<i class="fa fa-paperclip"></i>&nbsp; <?php echo $rs->NumAtCard; ?>
								<?php else : ?>
									<?php echo $rs->NumAtCard; ?>
								<?php endif; ?>
							</td>
							<td class="middle text-right"><?php echo number($rs->DocTotal, 2); ?></td>
							<td class="middle text-center"><?php echo $rs->credit_issue == 1 ? '<span class="red">Yes</span>' : 'No'; ?></td>
							<td class="middle text-center"><?php echo $rs->credit_issue == 1 ? ($rs->is_over_due == 1 ? '<span class="red">Yes</span>' : 'No') : ''; ?></td>
							<td class="middle text-center"><span class="btn btn-minier btn-primary btn-block" onclick="preview('<?php echo $rs->code; ?>')">Preview</span></td>
							<td class="middle text-center">
								<?php if ($rs->Status == 2) : ?>
									<a href="javascript:void(0)" class="green" onclick="viewDetail('<?php echo $rs->code; ?>')">Success</a>
								<?php endif; ?>
								<?php if ($rs->Status == 3) : ?>
									<a href="javascript:void(0)" class="red" onclick="viewDetail('<?php echo $rs->code; ?>')">Failed</a>
								<?php endif; ?>
								<?php if ($rs->Status == 1) : ?>
									<a href="javascript:void(0)" class="orange" onclick="viewDetail('<?php echo $rs->code; ?>')">Pending</a>
								<?php endif; ?>
								<?php if ($rs->Status == 0) : ?>
									<span class="text-center">Not Exported</span>
								<?php endif; ?>
								<?php if ($rs->Status == -1) : ?>
									<span class="red text-center">Canceled</span>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if ($rs->must_approve == 1) : ?>
									<button class="btn btn-minier btn-primary" onclick="showAuthorize('<?php echo $rs->code; ?>')">Authorizer</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if ($rs->must_approve == 1 && $rs->Approved == 'A') : ?>
									<?php if ($rs->Approval_status === 'P') : ?>
										<span class="green">อนุมัติบางส่วน</span>
										<!-- <span class="label label-lg label-success btn-block">อนุมัติบางส่วน</span> -->
									<?php else : ?>
										<span class="green">อนุมัติ</span>
										<!-- <span class="label label-lg label-success btn-block">อนุมัติ</span> -->
									<?php endif; ?>
								<?php elseif ($rs->must_approve == 1 && $rs->Approved == 'P') : ?>
									<span class="orange">รออนุมัติ</span>
									<!-- <span class="label label-lg label-warning btn-block">รออนุมัติ</span> -->
								<?php elseif ($rs->must_approve == 1 && $rs->Approved == 'R') : ?>
									<span class="red">ไม่อนุมัติ</span>
									<!-- <span class="label label-lg label-danger btn-block">ไม่อนุมัติ</span> -->
								<?php else : ?>
									<span class="green">อนุมัติ</span>
									<!-- <span class="label label-lg label-success btn-block">อนุมัติ</span> -->
								<?php endif; ?>
							</td>
							<td class="middle"><?php echo $rs->Approver; ?></td>
							<td class="middle text-center"><?php echo $rs->DocNum; ?></td>
							<td class="middle text-center">
								<?php if ($rs->SO_Status == 'D') : ?>
									<span class="red">Cancelled</span>
								<?php else : ?>
									<?php echo $rs->SO_Status == 'C' ? 'Closed' : ($rs->SO_Status == 'O' ? 'Open' : ''); ?>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo $rs->DO_Status == 'P' ? 'Partial' : ($rs->DO_Status == 'F' ? 'Full' : ''); ?></td>
							<td class="middle text-center"><?php echo $rs->INV_Status == 'P' ? 'Partial' : ($rs->INV_Status == 'F' ? 'Full' : ''); ?></td>
							<td class="middle"><?php echo sales_team_condition_name($rs->Condition_id); ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="20" class="middle text-center">ไม่พบรายการ</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


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
				<button type="button" class="btn btn-sm btn-success pull-left a-btn" id="btn-approve" onclick="doApprove()" disabled>อนุมัติ</button>
				<button type="button" class="btn btn-sm btn-danger pull-left a-btn" style="margin-left:25%;" id="btn-reject" onclick="doReject()" disabled>ไม่อนุมัติ</button>
				<button type="button" class="btn btn-sm btn-primary pull-left a-btn" id="btn-temp" onclick="sendToSAP()">Send To Temp</button>
				<button type="button" class="btn btn-sm btn-danger pull-right" id="btn-close" onClick="dismiss('previewModal')">Close</button>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="OrderCode" value="">
<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".jpg,.jpeg,.png,.pdf">

<script id="preview-template" type="text/x-handlebars-template">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
		<table class="table table-striped table-bordered border-1" style="margin-bottom:10px;">
			<tbody>
			<tr><td class="th fix-width-250">เลขที่ใบสั่งสินค้า</td><td>{{orderCode}}</td></tr>
			<tr><td class="th">User</td><td>{{user}}</td></tr>
			<tr><td class="th">รหัสลูกค้า</td><td>{{customerName}}</td></tr>
			<tr><td class="th">ที่อยู่ตามใบกำกับภาษี</td><td>{{billToCode}} | {{billToAddress}}</td></tr>
			<tr><td class="th">สถานที่ส่งของ</td><td>{{shipToCode}} | {{shipToAddress}}</td></tr>
			<tr><td class="th">สถานที่จัดส่งเพิ่มเติม</td><td>{{exShipTo}}</td></tr>
			<tr><td class="th">Currency</td><td>{{currency}} | Rate: {{currencyRate}} </td></tr>
			<tr><td class="th">วันที่สั่งสินค้า</td><td>{{docDate}}</td></tr>
			<tr><td class="th">วันที่จัดส่ง</td><td>{{dueDate}}</td></tr>
			<tr><td class="th">Promotion</th><td>{{promotionCode}}  |   {{promotionName}}</td></tr>
			<tr><td class="th">SO No.</td><td>{{SONO}}</td></tr>
			<tr><td class="th middle">เลขที่ PO <button type="button" class="btn btn-minier btn-success pull-right" title="Attached file and edit PO" onclick="showPoModal('{{orderCode}}', '{{PoNo}}', '{{Approved}}')"><i class="fa fa-plus"></i></button></td><td class="middle">{{PoNo}}  {{{fileName}}}</td></tr>
			<tr><td class="th">บิลลงวันที่</td><td>{{billOption}}</td></tr>
			<tr><td class="th">ต้องการใบเสนอราคา</td><td>{{requiredSQ}}</td></tr>
			<tr><td class="th">Order Type</td><td>{{isExport}}</td></tr>
			<tr><td class="th">Price List</td><td>{{PriceList}}</td></tr>
			<tr><td class="th">Payment Terms</td><td>{{termName}}</td></tr>
			<tr><td class="th">Remark สำหรับสื่อสารกับ Admin</td><td>{{remark}}</td></tr>
			</tbody>
		</table>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 padding-0 table-responsive" style="max-height:300px; overflow:auto;">
		<table class="table table-bordered border-1" style="min-width:1580px; margin-bottom:0px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="min-width-250 middle text-center">รายการสินค้า</th>
					<th class="fix-width-80 middle text-center">จำนวน</th>
					<th class="fix-width-80 middle text-center">แถม</th>
					<th class="fix-width-100 middle text-center">หน่วย</th>
					<th class="fix-width-100 middle text-center">ราคา/หน่วย (Term)</th>
					<th class="fix-width-100 middle text-center">ราคา(พิเศษ)/หน่วย</th>
					<th class="fix-width-80 middle text-center <?php echo $hide; ?>">Discount Sales</th>
					<th class="fix-width-100 middle text-center">มูลค่า</th>
					<th class="fix-width-100 middle text-center">หมายเหตุ</th>
					<th class="fix-width-100 middle text-center">จำนวนค้างส่ง</th>
					<th class="fix-width-100 middle text-center">เลขที่ DO</th>
					<th class="fix-width-100 middle text-center">เลขที่ Invoice</th>
					<th class="fix-width-100 middle text-center">วันที่ Invoice</th>
					<th class="fix-width-200 middle text-center">เหตุผลในการ Reject</th>
				</tr>
			</thead>
			<tbody>
				{{#each items}}
					<tr>
						<td class="middle text-center">
							{{{checkbox}}}
						</td>
						<td class="middle">{{itemName}}</td>
						<td class="middle text-right">{{qty}}</td>
						<td class="middle text-right">{{free}}</td>
						<td class="middle text-center">{{uom}}</td>
						<td class="middle text-right">{{stdPrice}}</td>
						<td class="middle text-right">{{sellPrice}}</td>
						<td class="middle text-center <?php echo $hide; ?>">{{{dis}}}</td>
						<td class="middle text-right">{{amount}}</td>
						<td class="middle">{{lineText}}</td>
						<td class="middle text-right">{{openQty}}</td>
						<td class="middle text-center">{{{DoNo}}}</td>
						<td class="middle text-center">{{{InvNo}}}</td>
						<td class="middle text-center">{{{InvDate}}}</td>
						<td class="middle">{{{rejectbox}}}</td>
					</tr>
				{{/each}}
			</tbody>
		</table>
	</div>
	<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 text-center" style="padding-top:30px;">
		{{{flow}}}
					
		{{#if isCancel}}
			<div style="margin-top:20px; padding-right:7px; display:flex; justify-content:center; align-items:center;">				
				<h4 class="status-label {{approval_color}}">{{approval_status}}</h4>			
			</div>
		{{/if}}
		{{#if isReject}}
			<div style="margin-top:20px; padding-right:7px; display:flex; justify-content:center; align-items:center;">				
				<h4 class="status-label {{approval_color}}">{{approval_status}}</h4>			
			</div>
		{{/if}}
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12" style="margin-top:20px; padding-right:7px;">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ราคาสินค้า</label>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.totalBefDi}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ส่วนลด [{{subTotal.DiscPrcnt}} %]</label>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.DiscSum}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ราคาสุทธิก่อนภาษีมูลค่าเพิ่ม</label>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.totalBefVat}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.totalVat}}" readonly>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">รวมเงินสุทธิ</label>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" value="{{subTotal.docTotal}}" readonly>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		{{#if ApproveBy}} {{ApproveBy}} {{/if}}
		{{#if isCancel}}<br/> Cancel By : {{cancel_by}} @ {{cancel_at}} {{/if}}
	</div>
</script>

<div class="modal fade" id="po-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:400px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title-site text-center" id="po-modal-title" style="margin-bottom:0px;">Add PO To Order</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<label class="col-lg-4 col-md-4 col-sm-4 col-xs-12 sap-label">เลขที่ PO</label>
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<input type="text" class="form-control input-sm" id="po-number" name="po-number" />
					</div>
					<div class="divider-hidden"></div>
					<label class="col-lg-4 col-md-4 col-sm-4 col-xs-12 sap-label">แนบ PO</label>
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control input-sm" id="attached-file-name" value="" readonly />
							<span class="input-group-btn">
								<button class="btn btn-white btn-xs btn-success btn-45" style="height:30px;" title="Attach PO file" type="button" onclick="getFile()"><i class="fa fa-paperclip fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-default btn-100" onClick="dismiss('po-modal')">Cancel</button>
				<button type="button" class="btn btn-white btn-success btn-100" onClick="addPo()">Save</button>
			</div>
		</div>
	</div>
</div>


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

<script id="authorizer-template" type="text/x-handlebarsTemplate">
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




<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:800px;">
		<div class="modal-content">
			<div class="modal-header" style="padding-bottom:0px;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Sales Sales Order Temp Status</h4>
			</div>
			<div class="modal-body" style="padding-top:5px;">
				<div class="row">
					<div class="col-sm-12 col-xs-12" id="temp-table">

					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<script id="temp-template" type="text/x-handlebarsTemplate">
	<input type="hidden" id="U_WEB_ORNO" value="{{U_WEB_ORNO}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">Web Order</td><td class="width-70">{{U_WEB_ORNO}}</td></tr>
      <tr><td class="width-30">BP Code</td><td class="width-70">{{CardCode}}</td></tr>
      <tr><td>BP Name</td><td>{{CardName}}</td></tr>
      <tr><td>Date/Time To Temp</td><td>{{F_WebDate}}</td></tr>
      <tr><td>Date/Time To SAP</td><td>{{F_SapDate}}</td></tr>
      <tr><td>Status</td><td>{{F_Sap}}</td></tr>
      <tr><td>Message</td><td>{{Message}}</td></tr>
			<tr>
				<td colspan="2">
				{{#if del_btn}}
					<button type="button" class="btn btn-sm btn-danger" onClick="cancelOrder()" ><i class="fa fa-times"></i> Cancel Order</button>
					<button type="button" class="btn btn-sm btn-warning" onClick="removeTemp()" ><i class="fa fa-trash"></i> Delete Temp</button>
				{{/if}}

				<button type="button" class="btn btn-sm btn-default pull-right" onclick="closeModal('tempModal')">Close</button>
				</td>
			</tr>
    </tbody>
  </table>
</script>

<style>
	.table>tr>td {
		white-space: nowrap;
	}
</style>
<script>
	$(document).ready(function() {
		setTimeout(function() {
			window.location.reload();
		}, 1000 * 60 * 10); //--- reload every 10 minutes
	});

	$('#user-id').select2();

	function showPoModal(orderCode, poNo = '', approved = 'P') {
		$('#OrderCode').val(orderCode);
		$('#po-modal-title').text(`Attache PO file for : ${orderCode}`);
		$('#po-number').val(poNo).removeAttr('disabled');

		if(approved == 'A' && poNo !== '') {
			$('#po-number').attr('disabled', 'disabled');
		}

		$('#uploadFile').val('');
		$('#previewModal').modal('hide');
		$('#po-modal').modal('show');
	}

	function getFile() {
		$('#uploadFile').click();
	}

	const uploadFile = document.getElementById('uploadFile');

	uploadFile.addEventListener('change', function() {
		if (this.files.length > 0) {
			const file = this.files[0];
			if (file.size > 5000000) {
				swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
				this.value = '';
				return false;
			}

			document.getElementById('attached-file-name').value = file.name;
		}
	});

	function addPo() {
		const orderCode = document.getElementById('OrderCode').value;
		const poCode = document.getElementById('po-number').value;
		const uploadFile = document.getElementById('uploadFile');
		const file = uploadFile.files[0];

		if(poCode === '') {
			swal("กรุณากรอกเลขที่ PO", "", "warning");
			return false;
		}

		if(!file) {
			swal("กรุณาเลือกไฟล์แนบ", "", "warning");
			return false;
		}

		const fd = new FormData();

		fd.append('orderCode', orderCode);
		fd.append('poCode', poCode);
		fd.append('uploadFile', file);

		$('#po-modal').modal('hide');

		load_in();

		$.ajax({
			url: `${HOME}add_po`,
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			success: function(rs) {
				load_out();

				if (isJson(rs)) {
					var ds = JSON.parse(rs);

					if (ds.status == 'success') {
						swal({
							title: 'Success',
							type: 'success',
							timer: 1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1100);
					} 
					else {
						showError(ds.message);
					}
				} 
				else {
					showError(rs);
				}
			},
			error: function(rs) {
				showError(rs);
			}
		});
	}
</script>

<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>