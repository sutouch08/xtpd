<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if ($this->pm->can_add) : ?>
				<button type="button" class="btn btn-white btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
			<?php endif; ?>
		</p>
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

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">All</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Can Approve</label>
			<select class="form-control input-sm filter" name="can_approve">
				<option value="all">All</option>
				<option value="1" <?php echo is_selected('1', $can_approve); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $can_approve); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Can Review</label>
			<select class="form-control input-sm filter" name="can_review">
				<option value="all">All</option>
				<option value="1" <?php echo is_selected('1', $can_review); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $can_review); ?>>No</option>
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
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped tableNarrow border-1" style="min-width:970px;">
			<thead>
				<tr>
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle">Username</th>
					<th class="min-width-200 middle">Employee</th>
					<th class="fix-width-100 middle">Max. Amount</th>
					<th class="fix-width-50 middle text-center">Status</th>
					<th class="fix-width-50 middle text-center">Approve</th>
					<th class="fix-width-50 middle text-center">Review</th>
					<th class="fix-width-130 middle">Last Modified</th>
					<th class="fix-width-150 middle">Modified By</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach ($data as $rs) : ?>
						<?php $last_modified = empty($rs->date_upd) ? thai_date($rs->date_add, TRUE) : thai_date($rs->date_upd, TRUE); ?>
						<?php $modified_by = empty($rs->date_upd) ? uname($rs->add_by) : uname($rs->update_by); ?>
						<tr>
							<td class="middle">
								<?php if ($this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->id; ?>)">
										<i class="fa fa-pencil"></i>
									</button>
									<?php endif; ?>
									<?php if ($this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->uname; ?>')">
										<i class="fa fa-trash"></i>
									</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->uname; ?></td>
							<td class="middle"><?php echo $rs->emp_name; ?></td>
							<td class="middle"><?php echo number($rs->amount, 2); ?></td>
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
							<td class="middle text-center">
								<?php if ($this->pm->can_edit) : ?>
									<label style="height: 22px;">
										<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="toggleCanApprove(<?php echo $rs->id; ?>, this)" <?php echo $rs->can_approve ? 'checked' : ''; ?>>
										<span class="lbl"></span>
									</label>
								<?php else : ?>
									<?php echo is_active($rs->can_approve); ?>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if ($this->pm->can_edit) : ?>
									<label style="height: 22px;">
										<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="toggleCanReview(<?php echo $rs->id; ?>, this)" <?php echo $rs->can_review ? 'checked' : ''; ?>>
										<span class="lbl"></span>
									</label>
								<?php else : ?>
									<?php echo is_active($rs->can_review); ?>
								<?php endif; ?>
							</td>
							<td class="middle"><?php echo $last_modified; ?></td>
							<td class="middle"><?php echo $modified_by; ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/credit_approver/credit_approver.js"></script>

<?php $this->load->view('include/footer'); ?>