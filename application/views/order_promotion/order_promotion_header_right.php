<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
    <div class="form-horizontal">

      <div class="form-group">
        <label class="col-lg-7 col-md-7 col-sm-7 col-xs-6 control-label no-padding-right">Username</label>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <input type="text" id="username" class="form-control input-sm" value="<?php echo $this->_user->uname; ?>" disabled/>
        </div>
      </div>


      <div class="form-group">
        <label class="col-lg-7 col-md-7 col-sm-7 col-xs-12 control-label no-padding-right">Order No.</label>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <input type="text" id="code" class="form-control input-sm" value="<?php echo $code; ?>" disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-7 col-md-7 col-sm-7 col-xs-12 control-label no-padding-right">วันที่สั่งสินค้า</label>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <span class="input-icon input-icon-right">
          <input type="text" id="DocDate" class="form-control input-sm text-center" value="<?php echo date('d-m-Y'); ?>" disabled/>
          <i class="ace-icon fa fa-calendar-o"></i>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-7 col-md-7 col-sm-7 col-xs-12 control-label no-padding-right">วันที่ต้องการจัดส่ง</label>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <span class="input-icon input-icon-right">
          <input type="text" id="DocDueDate" class="form-control input-sm text-center" value="<?php echo date('d-m-Y', strtotime(shift_date(now(), 3, TRUE))); ?>" readonly/>
          <i class="ace-icon fa fa-calendar-o"></i>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-7 col-md-7 col-sm-7 col-xs-12 control-label no-padding-right">เลขที่ PO</label>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <input type="text" id="PoNo" class="form-control input-sm" value=""/>
        </div>
      </div>
    </div>
</div>
