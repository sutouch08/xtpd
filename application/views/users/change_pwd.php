<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><i class="fa fa-lock"></i> <?php echo $this->title; ?></h4>
    </div>
</div><!-- End Row -->
<hr class="margin-bottom-30 padding-5"/>
<form class="form-horizontal" id="resetForm" method="post" action="<?php echo $this->home."/change_password"; ?>">
	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Employee name</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="dname" id="dname" class="width-100" value="<?php echo $data->emp_name; ?>" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="dname-error"></div>
  </div>



  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Username</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="uname" id="uname" class="width-100" value="<?php echo $data->uname; ?>" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error"></div>
  </div>


	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Current password</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="password" name="cpwd" id="cpwd" class="width-100" placeholder="รหัสผ่านปัจจุบัน" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="cpwd-error"></div>
  </div>


  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">New password</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="password" name="pwd" id="pwd" class="width-100" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="pwd-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Confirm password</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="password" name="cfpwd" id="cfpwd" class="width-100" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="cfpwd-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="changePassword()"><i class="fa fa-save"></i> เปลี่ยนรหัสผ่าน</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" id="use_strong_pwd" value="<?php echo getConfig('USE_STRONG_PWD'); ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/users/user_pwd.js"></script>
<?php $this->load->view('include/footer'); ?>
