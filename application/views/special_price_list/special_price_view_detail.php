<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-warning" onclick="goEdit(<?php echo $doc->id; ?>)"><i class="fa fa-pencil"></i> Edit</button>
		<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->

<hr class="padding-5"/>
<div class="row">
  <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 padding-5">
		<label>Price List Name</label>
		<input type="text" class="width-100 e" id="name" maxlength="100" value="<?php echo $doc->name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block">Status</label>
	   <input type="text" class="width-100 text-center" value="<?php echo $doc->active == 1 ? 'Active' : 'Inactive'; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-4-harf padding-5">
		<label>Update by</label>
		<input type="text" class="width-100 text-center" value="<?php echo $this->user_model->get_uname(empty($doc->update_by) ? $doc->add_by : $doc->update_by); ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4-harf padding-5">
		<label>Update at</label>
		<input type="text" class="width-100 text-center" value="<?php echo thai_date(empty($doc->update_by) ? $doc->date_add : $doc->date_upd, TRUE); ?>" disabled />
	</div>
</div>

<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
<hr class="">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:950px;">
			<thead>
				<tr>
					<th class="fix-width-50 middle"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-250 middle">Item Code</th>
					<th class="min-width-200 middle">Item Name</th>
					<th class="fix-width-80 middle text-center">Step</th>
					<th class="fix-width-80 middle text-center">Status</th>
				</tr>
			</thead>
			<tbody>
			<?php if( ! empty($details)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($details as $rs) : ?>
					<tr>
						<td class="middle">
							<button type="button" class="btn btn-minier btn-info" onclick="viewItem(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>							
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->ItemCode; ?></td>
						<td class="middle"><?php echo $rs->ItemName; ?></td>
						<td class="middle text-center"><?php echo $this->special_price_list_model->count_step($rs->id); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	$('#item').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/special_price_list/special_price_list.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
