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
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Group Name</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <input type="text" name="name" id="name" class="width-100 e" maxlength="50" value="<?php echo $group->name; ?>" readonly />
      <input type="hidden" name="id" id="id" value="<?php echo $group->id; ?>" />
    </div>    
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Status</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12"><?php echo $group->active == 1 ? 'Active' : 'Inactive'; ?></div>
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
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($lists)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($lists as $ps) : ?>
              <?php if (in_array($ps->id, $selected_lists)) : ?>
                <tr>
                  <td class="text-center"><?php echo $no; ?></td>
                  <td><?php echo $ps->name; ?></td>                  
                </tr>
                <?php $no++; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="<?php echo base_url(); ?>scripts/price_list_group/price_list_group.js?v=<?php echo date('Ymd'); ?>"></script>
  <?php $this->load->view('include/footer'); ?>