<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-white btn-success" onclick="syncData(0)"><i class="fa fa-refresh"></i> &nbsp; Sync</button>
      <button type="button" class="btn btn-white btn-primary" onclick="syncData(1)"><i class="fa fa-refresh"></i> &nbsp; Sync All</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Code</label>
      <input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Name</label>
      <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Head Code</label>
      <input type="text" class="form-control input-sm text-center search-box" name="cust_code" value="<?php echo $cust_code; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Department</label>
      <select class="form-control input-sm filter" id="department" name="department">
        <option value="">All</option>
        <?php echo select_department($department); ?>
      </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Area</label>
      <select class="form-control input-sm filter" id="area" name="area">
        <option value="">All</option>
        <?php echo select_area($area); ?>
      </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Sales Team</label>
      <select class="form-control input-sm filter" id="sales_team" name="sales_team">
        <option value="">All</option>
        <?php echo select_sales_team($sales_team); ?>
      </select>
    </div>
    <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>Sales Person</label>
      <select class="form-control input-sm filter" id="sales_person" name="sales_person">
        <option value="">All</option>
        <?php echo select_sales_person($sales_person); ?>
      </select>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Status</label>
      <select class="form-control input-sm filter" name="active">
        <option value="all" <?php echo is_selected('all', $active); ?>>All</option>
        <option value="Y" <?php echo is_selected('Y', $active); ?>>Active</option>
        <option value="N" <?php echo is_selected('N', $active); ?>>Inactive</option>
      </select>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Regular Customer</label>
      <select class="form-control input-sm filter" name="isRegular">
        <option value="all" <?php echo is_selected('all', $isRegular); ?>>All</option>
        <option value="1" <?php echo is_selected('1', $isRegular); ?>>Yes</option>
        <option value="0" <?php echo is_selected('0', $isRegular); ?>>No</option>
      </select>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
  <input type="hidden" name="search" value="1" />
  <input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
  <input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<?php $sort_code = get_sort('CardCode', $order_by, $sort_by); ?>
<?php $sort_name = get_sort('CardName', $order_by, $sort_by); ?>
<?php $sort_cust_code = get_sort('CustCode', $order_by, $sort_by); ?>
<?php $sort_update = get_sort('date_upd', $order_by, $sort_by); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped tableNarrow dataTable border-1" style="min-width:1280px;">
      <thead>
        <tr class="font-size-12">
          <th class="fix-width-60 text-center"></th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-50 text-center">Status</th>
          <th class="fix-width-120 sorting <?php echo $sort_code; ?>" id="sort-CardCode" onclick="sort('CardCode', '<?php echo $sort_code; ?>')">Code</th>
          <th class="min-width-250 sorting <?php echo $sort_name; ?>" id="sort-CardName" onclick="sort('CardName', '<?php echo $sort_name; ?>')">Name</th>
          <th class="fix-width-100 sorting <?php echo $sort_cust_code; ?>" id="sort-CustCode" onclick="sort('CustCode', '<?php echo $sort_cust_code; ?>')">Head Code</th>
          <th class="fix-width-50 text-center">Regular</th>
          <th class="fix-width-100">Department</th>
          <th class="fix-width-100">Area</th>
          <th class="fix-width-100">Sales Team</th>
          <th class="fix-width-150">Sales Person</th>
          <th class="fix-width-150 sorting <?php echo $sort_update; ?>" id="sort-date_upd" onclick="sort('date_upd', '<?php echo $sort_update; ?>')">Last Update</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php $departments = department_array(); ?>
          <?php $areas = area_array(); ?>
          <?php $sales_teams = sales_team_array(); ?>
          <?php $sales_persons = sales_person_array(); ?>
          <?php foreach ($data as $rs) : ?>
            <?php $department = isset($departments[$rs->U_TPD_BI_Department]) ? $departments[$rs->U_TPD_BI_Department] : NULL; ?>
            <?php $area = isset($areas[$rs->U_TPD_BI_AreaName]) ? $areas[$rs->U_TPD_BI_AreaName] : NULL; ?>
            <?php $sales_team = isset($sales_teams[$rs->U_TPD_BI_SalesTeam]) ? $sales_teams[$rs->U_TPD_BI_SalesTeam] : NULL; ?>
            <?php $sales_person = isset($sales_persons[$rs->SlpCode]) ? $sales_persons[$rs->SlpCode] : NULL; ?>
            <tr class="font-size-12">
              <td class="middle text-center">
                <button type="button" class="btn btn-minier btn-info" title="View Details" onclick="viewDetail(<?php echo $rs->id; ?>)">
                  <i class="fa fa-eye"></i>
                </button>
                <?php if ($this->pm->can_edit) : ?>
                  <button type="button" class="btn btn-minier btn-warning" title="Edit Customer" onclick="edit(<?php echo $rs->id; ?>)">
                    <i class="fa fa-pencil"></i>
                  </button>
                <?php endif; ?>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo is_active($rs->validFor, TRUE); ?></td>
              <td class="middle"><?php echo $rs->CardCode; ?></td>
              <td class="middle"><?php echo $rs->CardName; ?></td>
              <td class="middle"><?php echo $rs->CustCode; ?></td>
              <td class="middle text-center"><?php echo $rs->isRegular ? 'Yes' : 'No'; ?></td>
              <td class="middle"><?php echo $department; ?></td>
              <td class="middle"><?php echo $area; ?></td>
              <td class="middle"><?php echo $sales_team; ?></td>
              <td class="middle"><?php echo $sales_person; ?></td>
              <td class="middle"><?php echo thai_date($rs->date_upd, TRUE) ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  $('#department').select2();
  $('#area').select2();
  $('#sales_team').select2();
  $('#sales_person').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/customer/customer.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>