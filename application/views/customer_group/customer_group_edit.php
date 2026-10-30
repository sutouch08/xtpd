<?php $this->load->view('include/header'); ?>
<style>
  .tableNarrow>tbody>tr>td {
    font-size: 12px;
    padding: 2px 5px !important;
  }

  .box-title {
    border: solid 1px #ccc;
    padding: 10px;
    background-color: #f5f5f5;
    margin-bottom: 30px;
  }
</style>
<?php
$tab = isset($tab) ? $tab : 'info';
$info = $tab === 'info' ? 'active in' : '';
$customers = $tab === 'customers' ? 'active in' : '';
?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
      <button type="button" class="btn btn-sm btn-purple" title="Download Template" onclick="getImportTemplate()"><i class="fa fa-download"></i> Template</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-0" />
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5 padding-right-0" style="padding-top:5px;">
    <ul id="myTab1" class="setting-tabs" style="margin-left:0px;">
      <li class="li-block <?php echo $info; ?>" onclick="changeURL(<?php echo $data->id; ?>, 'info')"><a href="#info" data-toggle="tab">Customer Group</a></li>
      <li class="li-block <?php echo $customers; ?>" onclick="changeURL(<?php echo $data->id; ?>, 'customers')"><a href="#customers" data-toggle="tab">Members</a></li>
    </ul>
  </div>
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5" style="padding-top:5px; border-left:solid 1px #ccc; min-height:600px; max-height:1500px;">
    <div class="tab-content" style="border:0px;">
      <div class="tab-pane fade <?php echo $info; ?>" id="info">
        <div class="form-horizontal">
          <div class="form-group margin-top-30">
            <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Code</label>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
              <input type="text" id="code" class="width-100 e" maxlength="20" value="<?php echo $data->code; ?>" autocomplete="off" />
              <input type="hidden" id="id" value="<?php echo $data->id; ?>" />
            </div>
            <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
          </div>
          <div class="form-group">
            <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Description</label>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
              <input type="text" id="name" class="width-100 e" maxlength="50" value="<?php echo $data->name; ?>" autocomplete="off" />
            </div>
            <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
          </div>

          <div class="form-group">
            <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
              <label class="fix-width-100">
                <input type="radio" class="ace" name="status" value="1" <?php echo $data->active == 1 ? 'checked' : ''; ?> />
                <span class="lbl">&nbsp; &nbsp;Active</span>
              </label>
              <label class="fix-width-100">
                <input type="radio" class="ace" name="status" value="0" <?php echo $data->active == 0 ? 'checked' : ''; ?> />
                <span class="lbl">&nbsp; &nbsp;Inactive</span>
              </label>
            </div>
            <div class="help-block col-xs-12 col-sm-reset inline red" id="status-error"></div>
          </div>

          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>

          <div class="form-group">
            <div class="col-lg-7 col-lg-offset-3 col-md-9 col-lg-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
              <button type="button" class="btn btn-sm btn-success btn-100" onclick="update()">Update</button>
            </div>
          </div>
        </div>
      </div><!--/ tab-pane  -->

      <div class="tab-pane fade <?php echo $customers; ?>" id="customers">
        <div class="row">
          <div class="col-lg-5-harf col-md-5-harf col-sm-5-harf col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
              <h5 class="box-title text-center">Members List</h5>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 padding-5" style="padding-left: 0px;">
              <input type="text" class="form-control input-sm" id="group-search-box" placeholder="Search..." />
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-0 padding-left-5">
              <button type="button" class="btn btn-xs btn-primary btn-block" onclick="clearSearchText('group-search-box')"><i class="fa fa-refresh"></i> Clear</button>
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
              Results: <span id="group-result"><?php echo empty($details) ? 0 : count($details); ?></span> records
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1" style="height:400px; overflow-x:hidden; overflow-y:scroll;">
              <table class="table table-striped tableNarrow tableFixHead" id="group-list-table" style="margin-left:-1px; margin-top:-1px;">
                <thead>
                  <tr>
                    <th class="fix-width-40 text-center fix-header">
                      <input type="checkbox" class="" onchange="toggleAllGroups(this)" />
                    </th>
                    <th class="fix-width-120 fix-header">Code</th>
                    <th class="fix-header">Name</th>
                  </tr>
                </thead>
                <tbody id="group-list-body">
                  <?php if (!empty($details)) : ?>
                    <?php foreach ($details as $rs) : ?>
                      <tr id="group-<?php echo $rs->id; ?>">
                        <td class="middle text-center">
                          <input type="checkbox" class="row-check" value="<?php echo $rs->id; ?>" data-code="<?php echo $rs->CardCode; ?>" />
                        </td>
                        <td class="middle"><?php echo $rs->CardCode; ?></td>
                        <td class="middle"><?php echo $rs->CardName; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs padding-5 text-center" style="padding-top: 200px;">
            <button type="button" class="btn btn-sm btn-primary btn-block margin-bottom-10" onclick="addToGroup()"><i class="fa fa-arrow-left"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeFromGroup()"><i class="fa fa-arrow-right"></i></button>
          </div>
          <div class="col-xs-12 visible-xs padding-5 text-center margin-top-10 margin-bottom-10">
            <button type="button" class="btn btn-sm btn-primary btn-50" onclick="addToGroup()"><i class="fa fa-arrow-up"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-50" onclick="removeFromGroup()"><i class="fa fa-arrow-down"></i></button>
          </div>

          <div class="col-lg-5-harf col-md-5-harf col-sm-5-harf col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
              <h5 class="box-title text-center">Customer List</h5>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 padding-5" style="padding-left: 0px;">
              <input type="text" class="form-control input-sm" id="customer-search-box" placeholder="Search..." />
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-0 padding-left-5">
              <button type="button" class="btn btn-xs btn-primary btn-block" onclick="clearSearchText('customer-search-box')"><i class="fa fa-refresh"></i> Clear</button>
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
              Results: <span id="list-result"><?php echo empty($list) ? 0 : count($list); ?></span> records
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1" style="height:400px; overflow-x:hidden; overflow-y:scroll;">
              <table class="table table-striped tableNarrow tableFixHead" id="customer-list-table" style="margin-left:-1px; margin-top:-1px;">
                <thead>
                  <tr>
                    <th class="fix-width-40 text-center fix-header">
                      <input type="checkbox" class="" onchange="toggleAllCust(this)" />
                    </th>
                    <th class="fix-width-120 fix-header">Code</th>
                    <th class="fix-header">Name</th>
                  </tr>
                </thead>
                <tbody id="customer-list-body">
                  <?php if (!empty($list)) : ?>
                    <?php foreach ($list as $rs) : ?>
                      <tr id="cust-<?php echo $rs->id; ?>">
                        <td class="middle text-center">
                          <input type="checkbox" class="row-check" value="<?php echo $rs->id; ?>" data-code="<?php echo $rs->CardCode; ?>" />
                        </td>
                        <td class="middle"><?php echo $rs->CardCode; ?></td>
                        <td class="middle"><?php echo $rs->CardName; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="divider"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
          <button type="button" class="btn btn-white btn-success btn-100 top-btn" onclick="updateGroupDetails()"><i class="fa fa-save"></i> Save</button>
          <span>--- OR ---</span>
          <button type="button" class="btn btn-white btn-primary btn-100 top-btn" onclick="getFile()"><i class="fa fa-upload"></i> Import</button>
          <span>--- OR ---</span>
          <button type="button" class="btn btn-white btn-purple btn-100 top-btn" onclick="exportGroupDetails()"><i class="fa fa-download"></i> Export</button>
        </div>        
      </div><!--/ tab-pane  -->
    </div><!--/ tab-content  -->
  </div><!--/ col-lg-10  -->
</div><!--/ row  -->

<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />

<form id="export-form" method="post" action="<?php echo $this->home; ?>/export_details">
  <input type="hidden" name="id" id="export-id" value="<?php echo $data->id; ?>" />
  <input type="hidden" name="token" id="token" value="" />
</form>

<script src=" <?php echo base_url(); ?>scripts/customer_group/customer_group.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>