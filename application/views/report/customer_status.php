<?php $this->load->view('include/header'); ?>
<style>
	tr.headline>td {
		font-size: 14px;
		font-weight: bold;
		padding-top: 10px;
	}

	tr.space>td {
		padding-top: 10px;
		padding-bottom: 10px;
	}

	tr>td:first-child {
		padding-right: 10px;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
		<button type="button" class="btn btn-white btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Export</button>
		<button type="button" class="btn btn-white btn-success hide" onclick="openRequestForm('1')"><i class="fa fa-send"></i> Request</button>
	</div>
</div><!-- End Row -->
<hr>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="result">
		<div class="alert alert-info text-center">
			<button type="button" class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			</button>
			<strong>แสดงเฉพาะลูกค้าไม่ประจำที่มีอายุเกิน 3 เดือนและเคยเปิด invoice แล้วอย่างน้อย 3 ใบ. </strong>
		</div>
	</div>
</div>

<form id="reportForm" method="post" action="<?php echo $this->home . '/do_export'; ?>">
	<input type="hidden" name="token" id="token" value="">
</form>

<div class="modal fade" id="request-modal" tabindex="-1" role="dialog" data-backdrop="false" aria-labelledby="myModalLabel">
	<div class="modal-dialog" style="width:750px; max-width:95vw;">
		<div class="modal-content">
			<div class="modal-header" id="request-header" style="border-bottom:solid 1px #e5e5e5; cursor: move;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center" id="request-title" style="cursor: move;">คำขอเปลี่ยนเป็นลูกค้าประจำ</h4>
				<input type="hidden" id="customer-code" value="">
				<input type="hidden" id="customer-name" value="">
				<input type="hidden" id="customer-id" value="">
			</div>
			<div class="modal-body" style="max-width:94vw; min-height:300px;">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<table class="width-100">
							<tr>
								<td colspan="7" class="font-size-18 text-center" style="padding-bottom:15px;" id="customer-title">CC0001 : บริษัท ตัวอย่าง จำกัด</td>
							</tr>
							<tr>
								<td class="fix-width-100 text-right" style="padding-right:10px;">วันที่สร้าง</td>
								<td class="fix-width-100"><input type="text" class="form-control input-sm text-label" id="create-date" value="2026-01-01" readonly></td>
								<td class="fix-width-100 text-right" style="padding-right:10px;">เป็นลูกค้ามาแล้ว</td>
								<td class="fix-width-100"><input type="text" class="form-control input-sm text-label" id="duration" value="356 วัน" readonly></td>
								<td class="fix-width-100 text-right" style="padding-right:10px;">เปิดบิลแล้ว</td>
								<td class="min-width-100"><input type="text" class="form-control input-sm text-label" id="invoice-count" value=" 14 ใบ" readonly></td>
							</tr>
						</table>
					</div>
					<div class="divider"></div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<table class="width-100">
							<tr class="headline">
								<td class="fix-width-40 text-right">1</td>
								<td colspan="3">วิธีการชำระเงิน</td>
							</tr>
							<tr class="space">
								<td></td>
								<td colspan="3">
									<div class="row">
										<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-method" value="เช็ค">
												<span class="lbl">&nbsp;&nbsp; เช็ค</span>
											</label>
										</div>
										<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-method" value="เงินโอน">
												<span class="lbl">&nbsp;&nbsp; เงินโอน</span>
											</label>
										</div>
										<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-method" value="โอนผ่านระบบ GFMIS">
												<span class="lbl">&nbsp;&nbsp; โอนผ่านระบบ GFMIS</span>
											</label>
										</div>
										<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-method" value="สแกน QR">
												<span class="lbl">&nbsp;&nbsp; สแกน QR</span>
											</label>
										</div>
										<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-method" value="อื่นๆ">
												<span class="lbl">&nbsp;&nbsp; อื่นๆ</span>
											</label>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="4"><input type="text" class="width-100 text-label text-center" style="color:red !important;" id="payment-error" value="" readonly></td>
							</tr>
							<tr class="headline">
								<td class="text-right">2</td>
								<td colspan="3">ขั้นตอนการรับชำระเงินของลูกค้า</td>
							</tr>
							<tr class="space">
								<td></td>								
								<td colspan="3">
									<div class="row">
										<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
											2.1 &nbsp;&nbsp;เงื่อนไขการรับวางบิล
										</div>
										<div class="col-lg-2 col-md-4 col-sm-4 col-xs-4 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-billing" value="1">
												<span class="lbl">&nbsp;&nbsp; ไม่วางบิล</span>
											</label>
										</div>
										<div class="col-lg-7 col-md-8 col-sm-8 col-xs-8 padding-5">
											<label>
												<input type="radio" class="ace" name="payment-billing" value="2">
												<span class="lbl">&nbsp;&nbsp; วางบิลทุก
													<input type="text" class="fix-width-120 input-xs text-center text-label"
														style="margin-left:5px; margin-right:5px; border-bottom:solid 1px #333333 !important; border-bottom-style:dashed !important;"
														id="billing-date" min="1" max="31">&nbsp;ของเดือน</span>
											</label>
										</div>
									</div>
								</td>
							</tr>
							<tr class="space">
								<td></td>								
								<td colspan="3">
									<div class="row">
										<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
											2.2 &nbsp;&nbsp;รอบการชำระเงิน
										</div>
										<div class="col-lg-9 col-md-9 col-sm-8 col-xs-6 padding-5">
											ทุก <input type="text" class="fix-width-150 input-xs text-center text-label"
												style="margin-left:5px; margin-right:5px; border-bottom:solid 1px #333333 !important; border-bottom-style:dashed !important;"
												id="payment-date" min="1" max="31">&nbsp;ของเดือน
										</div>
									</div>								
								</td>
							</tr>
							<tr>
								<td colspan="4"><input type="text" class="width-100 text-label text-center" style="color:red !important;" id="billing-error" value="" readonly></td>
							</tr>
						</table>
					</div>
					<div class="divider-hidden"></div>
					<div class="divider-hidden"></div>

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:30px;">
						<p class="logs-text">หมายเหตุ: คำรองจะถูกส่งไปยังอีเมล์ผู้ดูแลระบบ [<b class="blue"><?php echo getConfig('SMTP_EMAIL'); ?></b>]
							และ CC ถึงคุณที่อีเมล์ของคุณ [<b class="purple"><?php echo $this->_user->email ? $this->_user->email : 'ไม่พบอีเมล์ของคุณ'; ?></b>]
						</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-white btn-success" onclick="submitRequest()"><i class="fa fa-send"></i> ส่งคำร้อง</button>
			</div>
		</div>
	</div>
</div>

<script id="report-template" type="text/x-handlebarsTemplate">
	<table class="table table-striped tableNarrow border-1" style="min-width:800px;">
		<thead>
			<tr>
				<th class="fix-width-50 text-center">#</th>
				<th class="fix-width-100">Code</th>
				<th class="min-width-250">Name</th>
				<th class="fix-width-100">Create Date</th>
				<th class="fix-width-100 text-center">Duration (days)</th>
				<th class="fix-width-100 text-center">Invoice Count</th>
				<th class="fix-width-100 text-center">Action</th>
			</tr>
		</thead>
		<tbody>
			{{#each this}}
				{{#if nodata}}
					<tr>
						<td colspan="7" class="text-center">--- Not found ---</td>
					</tr>
				{{else}}
					<tr id="row-{{id}}">
						<td class="text-center no">{{no}}</td>
						<td class="middle">{{CardCode}}</td>
						<td class="middle">{{CardName}}</td>
						<td class="middle">{{CreateDate}}</td>
						<td class="middle text-center">{{Duration}}</td>
						<td class="middle text-center">{{InvoiceCount}}</td>
						<td class="middle text-center">
							<button type="button" 
							class="btn btn-xs btn-white btn-purple" 
							id="btn-request-{{id}}"
							data-id="{{id}}"
							data-code="{{CardCode}}"
							data-name="{{CardName}}"
							data-create="{{CreateDate}}"
							data-duration="{{Duration}}"
							data-invoice="{{InvoiceCount}}"
							onclick="openRequestForm('{{id}}')"><i class="fa fa-send"></i> ส่งคำร้อง</button>
						</td>
					</tr>
				{{/if}}
			{{/each}}
		</tbody>
	</table>
</script>

<script>
	$('#department').select2();
	$('#area').select2();
	$('#sales-team').select2();
	$('#sales-person').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/report/customer_status.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>