<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>	
</div><!-- End Row -->

<hr class="padding-5"/>

<div class="row">
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label>Price List</label>
		<input type="text" class="form-control input-sm" value="<?php echo price_list_name($doc->PriceList); ?>" disabled/>
		</select>		
	</div>
	<div class="col-lg-8 col-md-8 col-sm-7-harf col-xs-12 padding-5">
		<label>Description</label>
		<input type="text" class="form-control input-sm e" value="<?php echo $doc->name; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-12 padding-5">
		<label>Status</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->active == 1 ? 'Active' : 'Inactive'; ?>" disabled/>
	</div>
</div>

<hr class="padding-5 margin-top-15 margin-bottom-15">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="overflow:auto;">
	<table class="table table-bordered tableNarrow border-1" style="min-width:800px;">
		<thead>
			<tr>
				<th class="fix-width-40 text-center">#</th>
				<th class="min-width-100 text-center">Label Text</th>
				<th class="fix-width-80 text-center">Step Qty</th>
				<th class="fix-width-80 text-center">Free Qty</th>
				<th class="fix-width-50 text-center">Status</th>
				<th class="fix-width-50 text-center">Force</th>
				<th class="fix-width-50 text-center">Highlight</th>
				<th class="fix-width-50 text-center">Position</th>
				<th class="fix-width-150 text-center">Update by</th>
				<th class="fix-width-150 text-center">Update at</th>
			</tr>
		</thead>
		<tbody id="detail-table">
			<?php $no = 1; ?>
			<?php if(!empty($details)) : ?>
				<?php foreach($details as $rs) : ?>
			<tr id="row-<?php echo $no; ?>">
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->labelText; ?></td>
				<td class="middle text-center"><?php echo number($rs->stepQty); ?></td>
				<td class="middle text-center"><?php echo number($rs->freeQty); ?></td>
				<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
				<td class="middle text-center"><?php echo is_active($rs->is_force, FALSE); ?></td>
				<td class="middle text-center"><?php echo is_active($rs->highlight, FALSE); ?></td>
				<td class="middle text-center"><?php echo $rs->position; ?></td>
				<td class="middle text-center">
					<?php if( ! empty($rs->update_by)) : ?>
						<?php echo $this->user_model->get_uname($rs->update_by); ?>
					<?php else : ?>
						<?php echo $this->user_model->get_uname($rs->add_by); ?>
					<?php endif; ?>
				</td>
				<td class="middle text-center">
					<?php if( ! empty($rs->update_by)) : ?>
						<?php echo thai_date($rs->date_upd, TRUE); ?>
					<?php else : ?>
						<?php echo thai_date($rs->date_add, TRUE); ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
		</tbody>
	</table>
</div>
</div>

<script src="<?php echo base_url(); ?>scripts/step_rule/step_rule.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
