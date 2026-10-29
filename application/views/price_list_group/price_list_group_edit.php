<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
      <?php if ($this->pm->can_edit) : ?>
        <button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()">Update</button>
      <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30" />

<form class="form-horizontal">
  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Group Name</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <input type="text" name="name" id="name" class="width-100 e" maxlength="50" value="<?php echo $group->name; ?>"  autocomplete="off" />
      <input type="hidden" name="id" id="id" value="<?php echo $group->id; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Status</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <label>
        <input type="radio" class="ace" name="status"  value="1" <?php echo $group->active == 1 ? 'checked' : ''; ?> />
        <span class="lbl">&nbsp; &nbsp;Active</span>
      </label>
      <label>
        <input type="radio" class="ace" name="status"  value="0" <?php echo $group->active == 0 ? 'checked' : ''; ?> />
        <span class="lbl">&nbsp; &nbsp;Inactive</span>
      </label>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="status-error"></div>
  </div>  

  <div class="divider"> </div>  

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Price List</label>
    <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 table-responsive" style="max-height: 500px; overflow-y: auto;">
      <table class="table table-striped table-bordered tableNarrow border-1" style="margin-bottom:0px;">
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
          <?php if (!empty($lists)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($lists as $ps) : ?>
              <tr>
                <td class="text-center"><?php echo $no; ?></td>
                <td><?php echo $ps->name; ?></td>
                <td class="text-center">
                  <label>
                    <input type="checkbox"
                      class="ace chk"
                      name="priceList[<?php echo $ps->id; ?>]"
                      id="priceList-<?php echo $ps->id; ?>"
                      value="<?php echo $ps->id; ?>" data-name="<?php echo $ps->name; ?>" <?php echo in_array($ps->id, $selected_lists) ? 'checked' : ''; ?>>
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

  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>

  <div class="form-group">
    <div class="col-lg-7 col-lg-offset-3 col-md-9 col-lg-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()">Update</button>
    </div>
  </div>

<script src="<?php echo base_url(); ?>scripts/price_list_group/price_list_group.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>