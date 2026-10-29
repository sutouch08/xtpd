<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<?php if ($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()">Update</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30" />

<form class="form-horizontal">
	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Username</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="uname" id="uname" class="width-100 e" maxlength="50" value="<?php echo $user->uname; ?>" onkeyup="validCode(this)" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Email</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="email" name="email" id="email" class="width-100 e" value="<?php echo $user->email; ?>" maxlength="100" autocomplete="off" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="email-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Employee</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" name="emp" id="emp">
				<option value="">Please select</option>
				<?php echo select_employee($user->emp_id); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="emp-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Sales Employee</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" name="saleman" id="saleman">
				<option value="">Please Select</option>
				<?php echo select_saleman($user->sale_id); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="saleman-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">User Group</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" name="ugroup" id="ugroup">
				<option value=""></option>
				<?php echo select_user_group($user->ugroup_id); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="ugroup-error"></div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			สำหรับกำหนด Permission ของ User
		</div>
	</div>


	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">User Role</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" name="u_role" id="u_role">
				<option value="sales" <?php echo is_selected($user->role, "sales"); ?>>Sales</option>
				<option value="salesAdmin" <?php echo is_selected($user->role, "salesAdmin"); ?>>Sales Admin</option>
				<option value="GM" <?php echo is_selected($user->role, "GM"); ?>>GM</option>
			</select>
		</div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			Sales : สามารถมองเห็นออเดอร์ของตัวเองและออเดอร์ใน Team ที่ตัวเองเป็น Team Lead เท่านั้น<br />
			Sales Admin : สามารถมองเห็นออเดอร์ทั้งหมด<br />
			GM : สามารถมองเห็นออเดอร์ทั้งหมดโดยไม่มีเงื่อนไข
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label ">User Area</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="area" id="area">
				<option value="">Please Select</option>
				<?php echo select_area($user->area_id); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			User จะสมารถมองเห็นออเดอร์ตาม area ที่กำหนดเท่านั้น และ รายชื่อลูกค้าจะถูกดึงมาให้เลือกตาม Area ที่กำหนดเท่านั้น
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Sales Team</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100 e" name="sale_team" id="sale-team">
				<option value="">Please Select</option>
				<?php echo select_sale_team($user->team_id); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sale-team-error"></div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			ใช้สำหรับ stamp ลงใน ออเดอร์ เพื่อกำหนดขอบเขตการมองเห็นของ user ที่เป็น Team Lead ในทีมเดียวกัน<br />
			กรณีที่ User role เป็น Sales Admin จะ stamp ลงในออเดอร์ ด้วย sales team ของลูกค้าแทน
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<label>
				<input type="checkbox" class="ace" name="status" id="status" value="1" <?php echo is_checked($user->status, 1); ?> />
				<span class="lbl">&nbsp; &nbsp;Active</span>
			</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<label>
				<input type="checkbox" class="ace" name="bi" id="bi" value="1" <?php echo is_checked(1, $user->bi_link); ?> />
				<span class="lbl">&nbsp; &nbsp;Power BI Access</span>
			</label>
		</div>
	</div>

	<div class="divider"></div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Price List</label>
		<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 table-responsive">
			<table class="table table-striped table-bordered border-1" style="margin-bottom:0px;">
				<thead>
					<tr>
						<th class="fix-width-50 text-center">#</th>
						<th class="">Price List</th>
						<th class="fix-width-50 text-center">
							<label>
								<input type="checkbox" class="ace" id="chk-all">
								<span class="lbl"></span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($price_list)) : ?>
						<?php $no = 1; ?>
						<?php foreach ($price_list as $ps) : ?>
							<?php $is_checked = isset($user->priceList[$ps->id]) ? TRUE : FALSE; ?>
							<tr>
								<td class="text-center"><?php echo $no; ?></td>
								<td><?php echo $ps->name; ?></td>
								<td class="text-center">
									<label>
										<input type="checkbox"
											class="ace chk"
											name="priceList[<?php echo $ps->id; ?>]"
											id="priceList-<?php echo $ps->id; ?>"
											value="<?php echo $ps->id; ?>" data-name="<?php echo $ps->name; ?>" <?php echo ($is_checked === TRUE ? "checked" : ""); ?>>
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
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			กำหนดว่า User นี้จะสามารถใช้ Price List ใดได้บ้าง
		</div>
	</div>

	<div class="divider"></div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Team Lead</label>
		<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 table-responsive">
			<table class="table table-striped table-bordered border-1" style="margin-bottom:0px;">
				<thead>
					<tr>
						<th class="fix-width-50 text-center ">ID</th>
						<th class="">Sales Team</th>
						<th class="fix-width-50 text-center">
							<label>
								<input type="checkbox" class="ace" id="team-all">
								<span class="lbl"></span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if (! empty($sales_team)) : ?>
						<?php $user_team = $user->user_team; ?>
						<?php foreach ($sales_team as $st) : ?>
							<tr>
								<td class="text-center"><?php echo $st->id; ?></td>
								<td><?php echo $st->name; ?></td>
								<td class="text-center">
									<label>
										<input type="checkbox"
											class="ace team"
											name="team[<?php echo $st->id; ?>]"
											id="team-<?php echo $st->id; ?>"
											value="<?php echo $st->id; ?>" data-name="<?php echo $st->name; ?>" <?php echo (empty($user_team[$st->id]) ? "" : "checked"); ?>>
										<span class="lbl"></span>
									</label>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12 grey">
			กำหนดให้ User นี้เป็น Team Lead ในทีมใดๆ จะทำให้ user นี้สามารถมองเห็นออเดอร์ของ User ทั้งหมดในทีมนั้นได้
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
			<button type="button" class="btn btn-sm btn-success btn-100 btn-xs-block" id="btn-save" onclick="update()">Update</button>
		</div>
	</div>

	<input type="hidden" name="id" id="id" value="<?php echo $user->id; ?>" />
	<input type="hidden" id="use_strong_pwd" value="<?php echo $strong_pwd; ?>" />

</form>

<script>
	$('#emp').select2();
	$('#saleman').select2();
	$('#area').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/users/users.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>