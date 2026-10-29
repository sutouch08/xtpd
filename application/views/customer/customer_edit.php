<?php $this->load->view('include/header'); ?>
<style>
  .form-group {
    margin-bottom: 10px;
  }
</style>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
  </div>
</div>
<hr />
<div class="row">
  <div class="form-horizontal">
    <div class="form-group margin-top-30">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Code</label>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $ds->CardCode; ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Head Code</label>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $ds->CustCode; ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Name</label>
      <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $ds->CardName; ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Department</label>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo department_name($ds->U_TPD_BI_Department); ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Area</label>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo area_name($ds->U_TPD_BI_AreaName); ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Sales Team</label>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo sales_team_name($ds->U_TPD_BI_SalesTeam); ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Sales Person</label>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo sales_person_name($ds->SlpCode); ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Credit Limit</label>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo number_format($ds->CreditLine, 2); ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Regular Customer</label>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12 padding-top-7">
        <label class="fix-width-100">
          <span class="lbl"><i class="fa <?php echo $ds->isRegular == 1 ? 'fa-check text-success' : 'fa-exclamation-triangle text-danger'; ?>"></i> &nbsp; <?php echo $ds->isRegular == 1 ? 'Yes' : 'No'; ?></span>
        </label>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 padding-top-7">
        <label class="fix-width-100">
          <span class="lbl"><i class="fa <?php echo $ds->validFor == 'Y' ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></i> &nbsp; <?php echo $ds->validFor == 'Y' ? 'Active' : 'Inactive'; ?></span>
        </label>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Registered Date</label>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 padding-top-7">
        <label class="fix-width-100">
          <span class="lbl"><?php echo thai_date($ds->registerDate); ?></span>
        </label>
      </div>
    </div>

    <div class="divider-hidden"></div>  
      
    <input type="hidden" id="id" value="<?php echo $ds->id; ?>">
    <input type="hidden" id="CardCode" value="<?php echo $ds->CardCode; ?>">
  </div>
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/customer/customer.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>