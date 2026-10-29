<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if ($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add new</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Name</label>
			<input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Type</label>
			<select class="form-control input-sm filter" name="type">
				<option value="all">All</option>
				<?php echo select_all_price_list_type($type); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-4 padding-5">
			<label>Start Date</label>
			<input type="date" class="form-control input-sm text-center" name="start_date" value="<?php echo $start_date; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-4 padding-5">
			<label>End Date</label>
			<input type="date" class="form-control input-sm text-center" name="end_date" value="<?php echo $end_date; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
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
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped tableNarrow border-1" style="min-width:1080px;">
			<thead>
				<tr>
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-70 middle text-center">Status</th>
					<th class="min-width-200 middle">Price List Name</th>
					<th class="fix-width-100 middle">Type</th>
					<th class="fix-width-130 middle">Start Date</th>
					<th class="fix-width-130 middle">End Date</th>
					<th class="fix-width-60 middle text-center">Items</th>
					<th class="fix-width-130 middle">Last Modified</th>
					<th class="fix-width-120 middle">Modified By</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php $typeName = price_list_type_array(); ?>
					<?php foreach ($data as $rs) : ?>
						<?php $rs->type_name = isset($typeName[$rs->type_id]) ? $typeName[$rs->type_id] : ''; ?>
						<?php $last_modified = empty($rs->date_upd) ? $rs->date_add : $rs->date_upd; ?>
						<?php $modified_by = empty($rs->update_by) ? uname($rs->add_by) : uname($rs->update_by); ?>
						<tr>
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
								<?php if ($this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->id; ?>)">
										<i class="fa fa-pencil"></i>
									</button>
								<?php endif; ?>
								<?php if ($this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')">
										<i class="fa fa-trash"></i>
									</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center">
								<label style="height: 22px;">
									<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="toggleActive(<?php echo $rs->id; ?>, this)" <?php echo $rs->active ? 'checked' : ''; ?>>
									<span class="lbl"></span>
								</label>								
							</td>
							<td class="middle"><?php echo $rs->name; ?></td>
							<td class="middle"><?php echo $rs->type_name; ?></td>
							<td class="middle"><?php echo empty($rs->start_date) ? '' : thai_date($rs->start_date, TRUE); ?></td>
							<td class="middle"><?php echo empty($rs->end_date) ? '' : thai_date($rs->end_date, TRUE); ?></td>
							<td class="middle text-center"><?php echo $this->special_price_list_model->count_items($rs->id); ?></td>
							<td class="middle"><?php echo thai_date($last_modified, TRUE); ?></td>
							<td class="middle"><?php echo $modified_by; ?></td>
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