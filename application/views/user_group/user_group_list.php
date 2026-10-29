<?php $this->load->view('include/header'); ?>
<?php $pm = get_permission('PERMISSION', $this->_user->uid, $this->_user->ugroup_id); ?>
<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h4>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-4 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add New</button>
      <?php endif; ?>
      </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>Name</label>
    <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
  </div>

  <div class="col-md-2 col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-md-2 col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:300px;">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="middle">Name</th>
					<th class="width-10 middle text-center">Member</th>
					<th class="width-20 middle text-right"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo number($rs->member); ?></td>
						</td>
						<td class="text-right">
							<button type="button" class="btn btn-xs btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
								<i class="fa fa-eye"></i>
							</button>
							<?php if($this->pm->can_edit) : ?>
							<button type="button" class="btn btn-xs btn-warning" onclick="goEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>

							<?php if($pm->can_add OR $pm->can_edit OR $pm->can_delete) : ?>
								<button type="button" class="btn btn-xs btn-primary" onclick="editPermission(<?php echo $rs->id; ?>)">&nbsp;<i class="fa fa-unlock-alt"></i>&nbsp;</button>
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

<?php $this->load->view('user_group/user_group_view_modal'); ?>


<script src="<?php echo base_url(); ?>scripts/user_group/user_group.js"></script>

<?php $this->load->view('include/footer'); ?>
