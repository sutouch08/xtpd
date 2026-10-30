<?php $this->load->view('include/header'); ?>
<?php $pm = get_permission('PERMISSION', $this->_user->uid, $this->_user->ugroup_id); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if ($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i> Add New</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 padding-5">
			<label>Name</label>
			<input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-narrow table-hover border-1" style="min-width:300px;">
			<thead>
				<tr>
					<th class="fix-width-100"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="min-width-200 middle">Name</th>
					<th class="fix-width-80 middle text-center">Member</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) : ?>
					<?php $no = $this->uri->segment(3) + 1; ?>
					<?php foreach ($data as $rs) : ?>
						<tr>
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
									<i class="fa fa-eye"></i>
								</button>
								<?php if ($this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>

								<?php if ($pm->can_add or $pm->can_edit or $pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-primary" onclick="editPermission(<?php echo $rs->id; ?>)">&nbsp;<i class="fa fa-unlock-alt"></i>&nbsp;</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->name; ?></td>
							<td class="middle text-center"><?php echo number($rs->member); ?></td>
							</td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('user_group/user_group_view_modal'); ?>


<script src="<?php echo base_url(); ?>scripts/user_group/user_group.js"></script>

<?php $this->load->view('include/footer'); ?>