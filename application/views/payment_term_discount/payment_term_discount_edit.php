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

<hr class="padding-5" />

<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Payment Terms</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" id="payment-term">
				<option value="">Select</option>
				<option value="-10" <?php echo is_selected('-10', $doc->GroupNum); ?>>Customer Default</option>
				<?php echo select_payment_term($doc->GroupNum); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="payment-term-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Name</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="name" class="width-100 e" maxlength="100" value="<?php echo $doc->name; ?>" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Discount</label>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12">
			<div class="input-group width-100">
				<input type="number" id="disc" class="width-100 text-center e" value="<?php echo $doc->DiscPrcnt; ?>" />
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
						<th class="fix-width-50 text-center fix-header">
							<label>
								<input type="checkbox" class="ace" onchange="toggleCheckPriceListAll(this)">
								<span class="lbl"></span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; ?>
					<?php if (! empty($priceList)) : ?>
						<?php foreach ($priceList as $ps) : ?>
							<?php $checked = empty($term_price_list[$ps->id][0]) ? "" : "checked"; ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td><?php echo $ps->name; ?></td>
								<td class="text-center">
									<label>
										<input type="checkbox"
											class="ace chk pl-chk"
											value="<?php echo $ps->id; ?>" data-spid="0" <?php echo $checked; ?> />
										<span class="lbl"></span>
									</label>
								</td>
							</tr>
							<?php $no++; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			กำหนดว่า Payment term นี้ จะสามารถใช้กับ Price List ใดได้บ้าง
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
						<th class="fix-width-50 text-center fix-header">
							<label>
								<input type="checkbox" class="ace" onchange="toggleCheckSpecialPriceListAll(this)">
								<span class="lbl"></span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; ?>
					<?php if (!empty($specialPriceList)) : ?>
						<?php foreach ($specialPriceList as $sp) : ?>
							<?php $checked = empty($term_price_list['x'][$sp->id]) ? "" : "checked"; ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td><?php echo $sp->name; ?></td>
								<td class="text-center">
									<label>
										<input type="checkbox"
											class="ace chk sp-chk"
											value="x" data-spid="<?php echo $sp->id; ?>" <?php echo $checked; ?>>
										<span class="lbl"></span>
									</label>
								</td>
							</tr>
							<?php $no++; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			กำหนดว่า Payment term นี้ จะสามารถใช้กับ Special Price List ใดได้บ้าง
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Position</label>
		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
			<select class="width-100" id="position">
				<option value="1" <?php echo is_selected($doc->position, '1'); ?>>1</option>
				<option value="2" <?php echo is_selected($doc->position, '2'); ?>>2</option>
				<option value="3" <?php echo is_selected($doc->position, '3'); ?>>3</option>
				<option value="4" <?php echo is_selected($doc->position, '4'); ?>>4</option>
				<option value="5" <?php echo is_selected($doc->position, '5'); ?>>5</option>
				<option value="6" <?php echo is_selected($doc->position, '6'); ?>>6</option>
				<option value="7" <?php echo is_selected($doc->position, '7'); ?>>7</option>
				<option value="8" <?php echo is_selected($doc->position, '8'); ?>>8</option>
				<option value="9" <?php echo is_selected($doc->position, '9'); ?>>9</option>
				<option value="10" <?php echo is_selected($doc->position, '10'); ?>>10</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label"></label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<label>
				<input type="checkbox" class="ace" id="allow-change" value="1" <?php echo is_checked('1', $doc->canChange); ?> />
				<span class="lbl">&nbsp; &nbsp;Allow Change</span>
			</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label"></label>
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-top-7">
			<label class="fix-width-100">
				<input type="radio" class="ace" name="active" value="1" <?php echo is_checked('1', $doc->active); ?> />
				<span class="lbl">&nbsp; &nbsp;Active</span>
			</label>
			<label class="fix-width-100">
				<input type="radio" class="ace" name="active" value="0" <?php echo is_checked('0', $doc->active); ?> />
				<span class="lbl">&nbsp; &nbsp;Inactive</span>
			</label>
		</div>
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
			<button type="button" class="btn btn-white btn-success btn-100 btn-xs-block" id="btn-save" onclick="update()">Update</button>
		</div>
	</div>
</div>
<script src="<?php echo base_url(); ?>scripts/payment_term_discount/payment_term_discount.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>