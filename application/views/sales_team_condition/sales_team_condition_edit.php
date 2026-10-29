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

<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Condition Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="form-control input-sm e" maxlength="100" value="<?php echo $data->name; ?>" autofocus required />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">BI Sales Team</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm e" name="team_id" id="team-id">
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
						<th class="text-center fix-header">Area Name</th>
						<th class="width-20 text-center fix-header">
							<label>
								<input type="checkbox" class="ace" id="area-all">
								<span class="lbl"></span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if( ! empty($area)) : ?>
						<?php $con_area = $data->area; ?>
						<?php foreach($area as $ar)  : ?>
							<tr>
								<td class="text-center"><?php echo $ar->id; ?></td>
								<td><?php echo $ar->name; ?></td>
								<td class="text-center">
									<label>
										<input type="checkbox"
										class="ace area"
										name="area[<?php echo $ar->id; ?>]"
										id="ar-<?php echo $ar->id; ?>"
										value="<?php echo $ar->id; ?>" data-name="<?php echo $ar->name; ?>" <?php echo (empty($data->area[$ar->id]) ? "" : "checked"); ?>>
										<span class="lbl"></span>
									</label>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>

	<div class="divider"></div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Authorizer</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8">
			<select class="form-control input-sm" name="approver" id="approver">
				<option value="">Please Select</option>
				<?php echo select_approver(); ?>
			</select>
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
			<button type="button" class="btn btn-primary btn-xs btn-block" onclick="addApprover()"><i class="fa fa-plus"></i> Add to list</button>
		</div>
		<div class="help-block col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 red" id="approver-error"></div>
  </div>

	<div class="form-group" id="authorizer-list">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="approver-list">
			<?php $no = 1; ?>
			<?php if(!empty($data->approver)) : ?>
				<?php foreach($data->approver as $rs) : ?>
					<label class="btn-block"  style="padding:10px; border:solid 1px #81a87b;" id="tag-<?php echo $no; ?>">
						<?php echo $rs->uname .' | '.$rs->emp_name; ?>
				    <a class="pointer bold pull-right red" onclick="removeTag(<?php echo $no; ?>)" style="margin-left:15px;">
				      <i class="fa fa-times"></i>
				    </a>
				  </label>
				  <input type="hidden" class="approver-list" name="approver-list" id="approver-<?php echo $no; ?>" value="<?php echo $rs->user_id; ?>"/>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
    </div>
  </div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

  <div class="form-group">
    <div class="col-lg-7 col-md-9 col-sm-9 col-xs-12 text-right">
    <button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>

	<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>">
	<input type="hidden" id="no" value="<?php echo $no; ?>">

	<script id="tag-template" type="text/x-handlebarsTemplate">
	  <label class="btn-block"  style="padding:10px; border:solid 1px #81a87b;" id="tag-{{no}}">
	    {{name}}
	    <a class="pointer bold pull-right red" onclick="removeTag({{no}})" style="margin-left:15px;">
	      <i class="fa fa-times"></i>
	    </a>
	  </label>
	  <input type="hidden" class="approver-list" name="approver-list" id="approver-{{no}}" value="{{user_id}}"/>
	</script>
</form>

<script src="<?php echo base_url(); ?>scripts/sales_team_condition/sales_team_condition.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
