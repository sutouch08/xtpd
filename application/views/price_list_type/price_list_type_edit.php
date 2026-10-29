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
<hr class="padding-5 margin-bottom-30" />

<form class="form-horizontal">
  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Type Name</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <input type="text" name="name" id="name" class="width-100 e" maxlength="50" value="<?php echo $data->name; ?>"  autocomplete="off" autofocus/>
    </div>
    <input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>" />
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
      <label class="fix-width-100">
        <input type="radio" class="ace" name="status"  value="1" <?php echo $data->active == 1 ? 'checked' : ''; ?> />
        <span class="lbl">&nbsp; &nbsp;Active</span>
      </label>
      <label class="fix-width-100">
        <input type="radio" class="ace" name="status"  value="0" <?php echo $data->active == 0 ? 'checked' : ''; ?> />
        <span class="lbl">&nbsp; &nbsp;Inactive</span>
      </label>
    </div>    
  </div>    
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>

  <div class="form-group">
    <div class="col-lg-7 col-lg-offset-3 col-md-9 col-lg-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()">Update</button>
    </div>
  </div>

<script src="<?php echo base_url(); ?>scripts/price_list_type/price_list_type.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>