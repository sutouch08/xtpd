<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>

<div class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Condition Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="form-control input-sm e" maxlength="100" value="<?php echo $data->name; ?>" disabled />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">BI Sales Team</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm e" name="team_id" id="team-id" disabled>
				<option value="">Please Select</option>
				<?php echo select_sale_team($data->team_id); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="team-id-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Area</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 table-responsive border-1 e" id="area" style="margin-left:5px; padding-left:0px; padding-right: 0px; max-height:300px; overflow:auto;">
			<table class="table table-striped table-bordered tableFixHead border-1" style="margin-top:-1px; margin-left:-1px;">
				<thead>
					<tr>
						<th class="width-20 text-center fix-header">ID</th>
						<th class="fix-header">Area Name</th>
					</tr>
				</thead>
				<tbody>
					<?php if( ! empty($area)) : ?>
						<?php $con_area = $data->area; ?>
						<?php $n = 1; ?>
						<?php foreach($area as $ar)  : ?>
							<?php if( ! empty($data->area[$ar->id])) : ?>
								<tr>
									<td class="text-center"><?php echo $n; ?></td>
									<td><?php echo $ar->name; ?></td>
								</tr>
								<?php $n++; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Authorizer</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 table-responsive border-1" style="margin-left:5px; padding-left:0px; padding-right: 0px; max-height:300px; overflow:auto;">
			<table class="table table-striped table-bordered tableFixHead border-1" style="margin-top:-1px; margin-left:-1px;">
				<thead>
					<tr>
						<th class="fix-width-40 text-center fix-header">#</th>
						<th class="fix-width-150 fix-header">User</th>
						<th class="min-width-100 fix-header">Name</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; ?>
					<?php if(!empty($data->approver)) : ?>
						<?php foreach($data->approver as $rs) : ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td class=""><?php echo $rs->uname; ?></td>
								<td><?php echo $rs->emp_name; ?></td>
							</tr>
							<?php $no++; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/sales_team_condition/sales_team_condition.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
