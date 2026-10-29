<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
      <?php endif; ?>
      </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-3 col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
    <label>Price List</label>
    <select class="form-control input-sm filter" name="price_list" id="price-list">
			<option value="all">ทั้งหมด</option>
			<?php echo select_price_list($price_list); ?>
		</select>
  </div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label>Status</label>
    <select class="form-control input-sm filter" name="status">
			<option value="all">All</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>Inactive</option>
		</select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped tableNarrow border-1" style="min-width:300px;">
			<thead>
				<tr>
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-70 middle text-center">Status</th>
					<th class="fix-width-200 middle">Price List</th>
					<th class="fix-width-80 middle text-center">Step</th>
					<th class="min-width-200 middle">Description</th>
					<th class="fix-width-150 middle">Last Modified</th>
					<th class="fix-width-150 middle">Modified By</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<?php $price_list_name = $priceList[$rs->PriceList]; ?>
					<tr>
						<td class="middle">
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->PriceList; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->PriceList; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->PriceList; ?>, '<?php echo $price_list_name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center">
							<?php if($this->pm->can_edit) : ?>
								<label style="height: 22px;">
									<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="toggleActive(<?php echo $rs->PriceList; ?>, this)" <?php echo is_checked($rs->active, 1); ?>>
									<span class="lbl"></span>
								</label>
							<?php else : ?>
								<?php echo is_active($rs->active); ?>
							<?php endif; ?>
						</td>
						<td class="middle"><?php echo $price_list_name; ?></td>
						<td class="middle text-center"><?php echo $this->step_rule_model->count_step($rs->PriceList); ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo empty($rs->date_upd) ? thai_date($rs->date_add, TRUE) : thai_date($rs->date_upd, TRUE); ?></td>
						<td class="middle"><?php echo empty($rs->update_by) ? uname($rs->add_by) : uname($rs->update_by); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$('#price-list').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/step_rule/step_rule.js?v=<?php echo date('Ymd'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
