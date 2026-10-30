<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if ($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i>&nbsp; Add New </button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Code</label>
			<input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>conditions</label>
			<select class="form-control input-sm" name="conditions" onchange="getSearch()">
				<option value="all">All</option>
				<?php echo select_conditions($conditions); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Amount</label>
			<input type="text" class="form-control input-sm text-center search-box" name="amount" value="<?php echo $amount; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Team Cond.</label>
			<select class="form-control input-sm" name="sale_team" onchange="getSearch()">
				<option value="all">All</option>
				<?php echo select_sales_team_condition($sale_team); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>Status</label>
			<select class="form-control input-sm" name="status" onchange="getSearch()">
				<option value="all">All</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1 col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1 col-xs-3 padding-5">
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
		<table class="table table-striped table-narrow table-hover border-1" style="min-width:700px;">
			<thead>
				<tr>
					<th class="fix-width-60 middle text-right">Actions</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="fix-width-100 middle">Code</th>
					<th class="fix-width-100 middle">Condition</th>
					<th class="fix-width-100 middle">Amount</th>
					<th class="min-width-200 middle">Team Cond.</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment(3) + 1; ?>
					<?php foreach ($data as $rs) : ?>
						<tr>
							<td class="middle">
								<?php if ($this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit(<?php echo $rs->id; ?>)">
										<i class="fa fa-pencil"></i>
									</button>
								<?php endif; ?>
								<?php if ($this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
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
							<td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $rs->conditions; ?></td>
							<td class="middle"><?php echo number($rs->amount, 2); ?></td>
							<td class="middle"><?php echo $rs->team_name; ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/approve_rule/approve_rule.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>