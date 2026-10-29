<?php $this->load->view('include/header'); ?>
<style>
	.tableFixHead thead th, .fix-header {
		outline:0;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div><!-- End Row -->

<hr class="padding-5" />

<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Payment Terms</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" id="payment-term" disabled>
				<option value="">Select</option>
				<?php echo select_payment_term($doc->GroupNum); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="payment-term-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Name</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="name" class="width-100 e" maxlength="100" value="<?php echo $doc->name; ?>" readonly />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Discount</label>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12">
			<div class="input-group width-100">
				<input type="number" id="disc" class="width-100 text-center e" value="<?php echo $doc->DiscPrcnt; ?>" readonly />
				<span class="input-group-addon">%</span>
			</div>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="disc-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Price List</label>
		<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 table-responsive padding-0 border-1" style="max-height:300px; overflow:auto;">
			<table class="table table-striped tableFixHead tableNarrow" style="margin-bottom:0px;">
				<thead>
					<tr>
						<th class="fix-width-50 text-center fix-header">#</th>
						<th class="fix-header">Price List</th>
						<th class="fix-width-50 text-center fix-header">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php if (! empty($term_list)) : ?>
						<?php $no = 1; ?>
						<?php foreach ($term_list as $list) : ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td><?php echo price_list_name($list->list_id); ?></td>
								<td class="text-center"><i class="fa fa-check green"></i></td>
							</tr>
							<?php $no++; ?>
						<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="3" class="text-center">ไม่พบรายการ</td>
							</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			กำหนดว่า Payment term นี้ จะสามารถใช้กับ Price List
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Special Price List</label>
		<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 table-responsive padding-0 border-1" style="max-height:300px; overflow:auto;">
			<table class="table table-striped tableFixHead tableNarrow" style="margin-bottom:0px;">
				<thead>
					<tr>
						<th class="fix-width-50 text-center fix-header">#</th>
						<th class="fix-header">Special Price List</th>
						<th class="fix-width-50 text-center fix-header">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php if (! empty($special_term_list)) : ?>
						<?php $no = 1; ?>
						<?php foreach ($special_term_list as $list) : ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td><?php echo special_price_list_name($list->special_price_id); ?></td>
								<td class="text-center"><i class="fa fa-check green"></i></td>
							</tr>
							<?php $no++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3" class="text-center">ไม่พบรายการ</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			กำหนดว่า Payment term นี้ จะสามารถใช้กับ Special Price List 
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Position</label>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-12">
			<input type="number" id="position" class="width-100 text-center e" value="<?php echo $doc->position; ?>" readonly />
		</div>			
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Allow Change</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-top-7">
			<label>
				<?php echo is_active($doc->canChange); ?>&nbsp; <?php echo $doc->canChange == 1 ? 'Yes' : 'No'; ?>
			</label>			
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-top-7">
			<label>
				<?php echo is_active($doc->active); ?>&nbsp; <?php echo $doc->active == 1 ? 'Active' : 'Inactive'; ?>
			</label>
		</div>
	</div>	
</div>
<script src="<?php echo base_url(); ?>scripts/payment_term_discount/payment_term_discount.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>