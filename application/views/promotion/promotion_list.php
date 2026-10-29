<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><i class="fa fa-bolt"></i>&nbsp; <?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> New Promotion</button>
      <?php endif; ?>
      </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Code</label>
    <input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>Name</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Start Date</label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm text-center" name="start_date" id="start_date" value="<?php echo $start_date; ?>" />
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>

  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>End Date</label>
		<div class="input-group width-100">
    <input type="text" class="form-control input-sm text-center" name="end_date" id="end_date" value="<?php echo $end_date; ?>" />
		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>
  </div>


	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">All</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
		</select>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:300px;">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="width-15 middle">Code</th>
					<th class="width-30 middle">name</th>
					<th class="width-12 middle">Start Date</th>
					<th class="width-12 middle">End Date</th>
					<th class="width-8 middle text-center">Status</th>
					<th class="middle text-right">Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo thai_date($rs->start_date); ?></td>
						<td class="middle"><?php echo thai_date($rs->end_date); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->status); ?></td>
						<td class="middle text-right">
							<button type="button" class="btn btn-xs btn-info" onclick="preview(<?php echo $rs->id; ?>)">Preview</button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-xs btn-warning" onclick="goEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-xs btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
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

<div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site" id="modal-title" style="margin-bottom:0px;" >Promotion Preview</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12" id="result">

                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="dismiss('preview-modal')" >Close</button>
            </div>
        </div>
    </div>
</div>


<script id="preview-template" type="text/x-handlebarsTemplate">
<table class="table table-striped table-bordered border-1" style="margin-bottom:0px;">
	<tr><td class="width-30">Code</td><td>{{code}}</td></tr>
	<tr><td>Name</td><td>{{name}}</td></tr>
	<tr><td>Date</td><td>{{start_date}} - {{end_date}}</td></tr>
	<tr><td>Status</td><td>{{status}}</td></tr>
</table>

<table class="table table-striped table-bordered border-1" style="margin-bottom:0px;">
	<thead>
		<tr>
			<th class="width-10 text-center">#</th>
			<th class="width-60">Items</th>
			<th class="width-15 text-center">Qty.</th>
			<th class="width-15 text-center">Price</th>
		</tr>
	</thead>
	<tbody>
		{{#each items}}
			<tr>
				<td class="middle text-center">{{no}}</td>
				<td class="middle">{{ItemName}}</td>
				<td class="middle text-center">{{Qty}}</td>
				<td class="middle text-center">{{Price}}</td>
			</tr>
		{{/each}}
	</tbody>
</table>
</script>



<script src="<?php echo base_url(); ?>scripts/promotion/promotion.js"></script>


<?php $this->load->view('include/footer'); ?>
