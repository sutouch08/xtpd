<?php $this->load->view('include/header'); ?>
<style>
	.form-group {
		margin-bottom: 5px;
	}

	.tableNarrow thead tr th {
		font-size: 11px;
	}

	.request-text {
		font-size: 12px;
		line-height: 1.15;		
		/* font-style: italic; */
	}

	.request-text > span {
		font-weight: bold;
		/* font-style: italic; */
		color:#505050;
		/* background-color:#e1e1e1; */
		padding: 2px 5px;
		/* border-radius: 3px; */
		font-size: 11px;
	}

	.log-text {
		margin-bottom:3px;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>WebOrder No.</label>
			<input type="text" class="form-control input-sm text-center search-box" name="WebCode" value="<?php echo $WebCode; ?>" placeholder="Web order" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>Customer</label>
			<input type="text" class="form-control input-sm text-center search-box" name="CardCode" value="<?php echo $CardCode; ?>" placeholder="Code OR Name" />
		</div>

		<div class="col-lg-3 col-md-5 col-sm-5 col-xs-12 padding-5">
			<label>User</label>
			<select class="form-control input-sm filter" name="user_id" id="user-id">
				<option value="all" <?php echo is_selected('all', $user_id); ?>>ทั้งหมด</option>
				<?php echo select_user_id($user_id); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<optgroup label="Before-Approve">				
					<option value="all" <?php echo is_selected('all', $status); ?>>ก่อนอนุมัติทั้งหมด</option>
					<option value="O" <?php echo is_selected('O', $status); ?>>รอดำเนินการ</option>
					<option value="N" <?php echo is_selected('N', $status); ?>>รอเอกสาร</option>
					<option value="R" <?php echo is_selected('R', $status); ?>>รอตรวจสอบ</option>
					<option value="P" <?php echo is_selected('P', $status); ?>>รออนุมัติ</option>
				</optgroup>
				<optgroup label="After-Approve">
					<option value="AA" <?php echo is_selected('AA', $status); ?>>หลังอนุมัติทั้งหมด</option>
					<option value="AP" <?php echo is_selected('AP', $status); ?>>อนุมัติ</option>
					<option value="AR" <?php echo is_selected('AR', $status); ?>>ไม่อนุมัติ</option>
				</optgroup>
			</select>
		</div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
			<label>Overdue</label>
			<select class="form-control input-sm filter" name="is_overdue">
				<option value="all" <?php echo is_selected('all', $is_overdue); ?>>ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $is_overdue); ?>>No</option>
				<option value="1" <?php echo is_selected('1', $is_overdue); ?>>Yes</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>WebOrder Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="from_date" value="<?php echo $from_date; ?>" placeholder="From" readonly />
				<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="to_date" value="<?php echo $to_date; ?>" placeholder="To" readonly />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>

	<input type="hidden" name="search" value="1" />
</form>

<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table tableNarrow border-1" style="min-width:1360px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-100">Actions</th>
					<th class="fix-width-80">Status</th>
					<th class="fix-width-40 text-center">Overdue</th>
					<th class="fix-width-40 text-center">Files</th>
					<th class="fix-width-100">WebOrder Date</th>
					<th class="fix-width-100">WebOrder</th>
					<th class="fix-width-100">User</th>
					<th class="min-width-250">Customer</th>
					<th class="fix-width-80 text-right">Doc total</th>
					<th class="fix-width-90 text-right">Credit Limit</th>
					<th class="fix-width-80 text-right">Credit Diff</th>
					<th class="fix-width-130">Request Date</th>
					<th class="fix-width-130">Reply Date</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach ($data as $rs) : ?>
						<?php $capv = $rs->credit_approval; ?>
						<?php $replyStatus = $rs->reply_status; ?>
						<?php //- P = รอดำเนินการ //-- D = รอเอกสาร //-- O = รอตรวจสอบ //-- A = รออนุมัติ  ?>
						<?php $status = $capv == 'O' ? ($replyStatus == 'R' ? $status = 'O' : ($replyStatus == 'N' ? $status = 'D' : 'P')) : 'A'; ?>
						<?php $color = $status == 'P' ? 'background-color:#ffdede;' : ($status == 'D' ? 'background-color:#fbe9ff;' : ($status == 'O' ? 'background-color:#e9fdff;' : 'background-color:#f4ffe9;')); ?>						
						<?php $text_color = ($rs->is_over_due) ? 'color:#bb1f1f;' : ''; ?>
						<tr style="<?php echo $color; ?> <?php echo $text_color; ?>">
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" title="Preview" onclick="preview('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<button type="button" class="btn btn-minier btn-primary" title="Authorizer" onclick="showAuthorize('<?php echo $rs->code; ?>')"><i class="fa fa-user"></i></button>
								<?php if ($capv == 'O' && $rs->is_over_due && empty($rs->credit_case_id) && $can_review) : ?>
									<button type="button" class="btn btn-minier btn-warning" title="Request Payment" onclick="addRequestPayment('<?php echo $rs->code; ?>')"><i class="fa fa-plus"></i></button>
								<?php endif; ?>
								<?php if (! empty($rs->credit_case_id)) : ?>
									<button type="button" class="btn btn-minier btn-success" title="View Request Payment" onclick="viewRequestPayment('<?php echo $rs->code; ?>')"><i class="fa fa-search"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle">
								<?php if ($capv == 'O') : ?>
									<?php if ($replyStatus == 'R') : ?>
										<span title="ผู้แทนแนบเอกสารแล้ว แต่ยังไม่ได้ตรวจสอบเอกสาร">รอตรวจสอบ</span>
									<?php elseif ($replyStatus == 'N') : ?>
										<span title="แจ้งผู้แทนแล้ว แต่ยังไม่ได้แนบเอกสาร">รอเอกสาร</span>
									<?php else : ?>
										<span title="มียอดค้างชำระเกินกำหนดและยังไม่ได้แจ้งผู้แทน">รอดำเนินการ</span>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($capv == 'P') : ?>
									<span title="ตรวจสอบเอกสารแล้ว แต่ยังไม่ได้อนุมัติ">รออนุมัติ</span>
								<?php endif; ?>
								<?php if ($capv == 'A') : ?>
									<span title="อนุมัติแล้ว">อนุมัติแล้ว</span>
								<?php endif; ?>
								<?php if ($capv == 'R') : ?>
									<span title="ปฏิเสธแล้ว">ปฏิเสธแล้ว</span>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo $rs->is_over_due ? 'Y' : 'N'; ?></td>
							<td class="middle text-center"><?php echo $rs->has_document ? 'Y' : 'N'; ?></td>
							<td class="middle"><?php echo thai_date($rs->date_add); ?></td>
							<td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $rs->uname; ?></td>
							<td class="middle"><?php echo $rs->CardCode; ?> | <?php echo $rs->CardName; ?></td>
							<td class="middle text-right"><?php echo number($rs->DocTotal, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->creditBalance, 2); ?></td>
							<td class="middle text-right">- <?php echo number($rs->credit_diff, 2); ?></td>
							<td class="middle"><?php echo empty($rs->request_date) ? NULL : thai_date($rs->request_date, TRUE); ?></td>
							<td class="middle"><?php echo empty($rs->reply_date) ? NULL : thai_date($rs->reply_date, TRUE); ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="14" class="middle text-center">ไม่พบรายการ</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<input type="hidden" id="OrderCode" value="">

<?php $this->load->view('credit_approval/credit_approval_modal'); ?>

<script>
	$('#user-id').select2();	
</script>

<script src="<?php echo base_url(); ?>scripts/credit_approval/credit_approval.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>