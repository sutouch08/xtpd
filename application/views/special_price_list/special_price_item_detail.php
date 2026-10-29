<?php $this->load->view('include/header'); ?>
<style>
	.tableFixHead {
		margin-top: -1px;
		margin-left: -1px;
	}

	.tableFixHead thead th {
		outline: 0;
	}

	.fix-header {
		outline: 0;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div><!-- End Row -->

<hr class="padding-5" />

<div class="row">
	<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<label>Item Code</label>
		<input type="text" class="width-100" id="item-code" value="<?php echo $item->ItemCode; ?>" disabled />
		<input type="hidden" id="id" value="<?php echo $item->id; ?>" />
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<label>Item Name</label>
		<input type="text" class="width-100" id="item-name" value="<?php echo $item->ItemName; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Uom</label>
		<input type="text" class="width-100" id="uom" value="<?php echo $item->UomCode; ?>" disabled />
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
		<label>Status</label>
		<input type="text" class="width-100 text-center" value="<?php echo $item->active == 1 ? 'Active' : 'Inactive'; ?>" disabled />
	</div>
</div>

<hr class="padding-5 margin-top-15 margin-bottom-15">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive" style="height:300px; overflow-y:scroll;">
		<table class="table table-striped tableFixHead tableNarrow" style="min-width:840px;">
			<thead>
				<tr class="">
					<th class="fix-width-40 text-center fix-header">#</th>
					<th class="min-width-200 fix-header">Description</th>
					<th class="fix-width-80 text-right fix-header">Min. Qty</th>
					<th class="fix-width-80 text-right fix-header">Sell Price</th>
					<th class="fix-width-80 text-right fix-header">Free Qty</th>
					<th class="fix-width-80 text-center fix-header">Position</th>
					<th class="fix-width-150 fix-header">Update by</th>
					<th class="fix-width-130 fix-header">Update at</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php $no = 1; ?>
				<?php if (!empty($details)) : ?>
					<?php foreach ($details as $rs) : ?>
						<tr id="row-<?php echo $no; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->name; ?></td>
							<td class="middle text-right"><?php echo number($rs->Qty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->SellPrice, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->freeQty, 2); ?></td>
							<td class="middle text-center"><?php echo $rs->position; ?></td>
							<td class="middle"><?php echo $this->user_model->get_uname($rs->update_by); ?></td>
							<td class="middle"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/special_price_list/special_price_list.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>