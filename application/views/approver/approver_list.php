<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if ($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success btn-top" onclick="addNew()"><i class="fa fa-plus"></i> Add new</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Username</label>
			<input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Employee</label>
			<input type="text" class="form-control input-sm text-center search-box" name="emp_name" value="<?php echo $emp_name; ?>" />
		</div>		

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">All</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
			</select>
		</div>

		<div class="divider-hidden visible-xs"></div>

		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8 padding-5">
			<label class="display-block not-show hidden-xs">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5">
			<label class="display-block not-show hidden-xs">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped tableNarrow border-1" style="min-width: 1200px; margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="fix-width-60 middle">Actions</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="fix-width-150 middle">Username</th>
					<th class="min-width-200 middle">Employee</th>
					<th class="fix-width-100 middle text-right">Min. Amount</th>
					<th class="fix-width-100 middle text-right">Max. Amount</th>
					<th class="fix-width-100 middle">Create by</th>
					<th class="fix-width-130 middle">Create at</th>
					<th class="fix-width-100 middle">Modified by</th>
					<th class="fix-width-130 middle">Modified at</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment(3) + 1; ?>
					<?php foreach ($data as $rs) : ?>
						<tr>
							<td class="middle">
								<?php if ($this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->id; ?>)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="btn btn-minier btn-danger" onclick="confirmDelete(<?php echo $rs->id; ?>, '<?php echo $rs->uname; ?>')">
										<i class="fa fa-trash"></i>
									</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center">
								<?php if ($this->pm->can_edit) : ?>
									<label style="height: 22px;">
										<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="toggleActive(<?php echo $rs->id; ?>, this)" <?php echo $rs->status ? 'checked' : ''; ?>>
										<span class="lbl"></span>
									</label>
								<?php else : ?>
									<?php echo is_active($rs->status); ?>
								<?php endif; ?>
							</td>
							<td class="middle"><?php echo $rs->uname; ?></td>
							<td class="middle"><?php echo $rs->emp_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->min_amount, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
							<td class="middle"><?php echo uname($rs->add_by); ?></td>
							<td class="middle"><?php echo thai_date($rs->date_add, TRUE); ?></td>
							<td class="middle"><?php echo uname($rs->update_by); ?></td>
							<td class="middle"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/approver/approver.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>