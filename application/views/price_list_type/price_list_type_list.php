<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> &nbsp; Add New</button>
      <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>Type Name</label>
      <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
      <label>Active</label>
      <select class="form-control input-sm filter" name="active">
        <option value="all" <?php echo is_selected('all', $active); ?>>All</option>
        <option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
        <option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
      </select>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
  <input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped tableNarrow border-1" style="min-width:780px;">
      <thead>
        <tr>
          <th class="fix-width-60"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-70 text-center">Status</th>
          <th class="fix-width-250">Type name</th>  
          <th class="fix-width-60 text-center">Position</th>        
          <th class="fix-width-150">Last modified</th>
          <th class="min-width-150">Modified By</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach ($data as $rs) : ?>
            <tr id="row-<?php echo $rs->id; ?>" >
              <td class="middle">                
                <?php if($this->pm->can_edit) : ?>
                  <button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>
                <?php if($this->pm->can_delete) : ?>
                  <button type="button" class="btn btn-minier btn-danger" onclick="confirmDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>              
              <td class="middle text-center">
                <label style="height:22px;">
                  <input class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="toggleActive(<?php echo $rs->id; ?>, this)" <?php echo $rs->active ? 'checked' : ''; ?>>
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="middle"><?php echo $rs->name; ?></td>
              <td class="middle text-center">
                <input type="number" class="form-control input-xs text-center" value="<?php echo $rs->position; ?>" onchange="updatePosition(<?php echo $rs->id; ?>, this)" />                
              </td>
              <td class="middle"><?php echo thai_date($rs->date_upd, TRUE) ?></td>
              <td class="middle"><?php echo empty($rs->update_by) ? uname($rs->create_by) : uname($rs->update_by); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/price_list_type/price_list_type.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>