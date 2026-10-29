<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><i class="fa fa-users"></i> <?php echo $this->title; ?></h4>
  </div>
	<div class="col-md-6 col-sm-6 col-xs-4 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>

<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Team Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="width-100" maxlength="100" value="" autofocus required />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Sale Person</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="sale_person" id="sale_person">
				<option value="">Please Select</option>
				<?php echo select_sale_person(); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sale-person-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Customer Team</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="customer_team" id="customer_team">
				<option value="">Please Select</option>
				<?php echo select_customer_team(); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="customer-team-error"></div>
  </div>



	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Customer Group</label>
    <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 table-responsive" style="height:400px; overflow-y:scroll">
			<table class="table table-striped table-bordered border-1">
				<thead>
					<tr>
						<th class="width-10 text-center">#</th>
						<th>Customer Group</th>
						<th class="width-10 text-center">
							<label>
								<input type="checkbox" class="ace" id="chk-all">
								<span class="lbl"></span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>

					<?php if(!empty($customer_group)) : ?>
						<?php $no = 1; ?>
						<?php foreach($customer_group as $cg)  : ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td><?php echo $cg->GroupName; ?></td>
								<td class="text-center">
									<label>
										<input type="checkbox"
										class="ace chk"
										name="customerGroup[<?php echo $cg->GroupCode; ?>]"
										id="customerGroup-<?php echo $cg->GroupCode; ?>"
										value="<?php echo $cg->GroupCode; ?>">
										<span class="lbl"></span>
									</label>
								</td>
							</tr>
							<?php $no++; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
    </div>
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

	<div class="form-group hide" id="authorizer-list">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="approver-list">
    </div>
  </div>


  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-2 col-lg-offset-2 col-md-2 col-md-offset-2 col-sm-2 col-sm-offset-2 col-xs-12">
    <button type="button" class="btn btn-sm btn-success btn-block" id="btn-save" onclick="saveAdd()"><i class="fa fa-save"></i> Add</button>
    </div>
  </div>

	<input type="hidden" id="no" value="1">

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

<script src="<?php echo base_url(); ?>scripts/sale_team/sale_team.js"></script>
<?php $this->load->view('include/footer'); ?>
