<div class="col-lg-3-harf col-md-4-harf col-sm-5 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 sap-label">Username</label>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <input type="text" id="username" class="form-control input-sm" value="<?php echo $this->_user->uname; ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 sap-label">Order No.</label>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <input type="text" id="code" class="form-control input-sm" value="<?php echo $code; ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 sap-label">เลขที่ PO</label>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <input type="text" id="PoNo" class="form-control input-sm e" value="" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 sap-label">Attach file</label>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <div class="input-group">
          <input type="text" id="attached-file-name" class="form-control input-sm e" value="" readonly />
          <span class="input-group-btn">
            <button type="button" class="btn btn-white btn-xs btn-success btn-45" id="btn-add-file" style="height:30px;" title="Attach PO file" onclick="getFile()"><i class="fa fa-plus fa-lg"></i></button>
            <button type="button" class="btn btn-white btn-xs btn-danger btn-45 hide" id="btn-clear-file" style="height:30px;" title="Clear attached file" onclick="clearImportFile()"><i class="fa fa-times fa-lg"></i></button>
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 sap-label">Price List</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-right-0">
        <select class="width-100" id="price-list-type" onchange="changePriceListType()">
          <option value="all">All</option>
          <?php echo select_price_list_type(); ?>
        </select>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <select class="width-100 e" name="priceList" id="priceList" onchange="checkPriceList()">
          <option value="">Select</option>
          <?php if (!empty($priceList)) : ?>
            <?php foreach ($priceList as $label => $list) : ?>
              <optgroup label="<?php echo $label; ?>">
                <?php foreach ($list as $pl) : ?>
                  <option value="<?php echo $pl->id; ?>" data-spid="<?php echo $pl->spid; ?>"><?php echo $pl->name; ?></option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 sap-label">วันที่สั่งสินค้า</label>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <span class="input-icon input-icon-right width-100">
          <input type="text" id="DocDate" class="form-control input-sm text-center e" value="<?php echo date('d-m-Y'); ?>" disabled />
          <i class="ace-icon fa fa-calendar-o"></i>
        </span>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 sap-label">วันที่ต้องการจัดส่ง</label>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-12">
        <span class="input-icon input-icon-right width-100">
          <input type="text" id="DocDueDate" class="form-control input-sm text-center e" value="<?php echo date('d-m-Y', strtotime(shift_date(now(), 3, TRUE))); ?>" readonly />
          <i class="ace-icon fa fa-calendar-o"></i>
        </span>
      </div>
    </div>

    <?php if ($this->isAdmin) : ?>
      <div class="form-group">
        <label class="col-lg-6 col-md-4-harf col-sm-4-harf col-xs-6 sap-label">Sale Employee</label>
        <div class="col-lg-6 col-md-7-harf col-sm-7-harf col-xs-6">
          <input type="text" class="width-100" id="slpName" disabled />
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>