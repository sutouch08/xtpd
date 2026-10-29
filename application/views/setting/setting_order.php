  <form id="orderForm">
    <div class="row">
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Auto Cancel orders</span></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <label style="padding-top:5px; margin-bottom:0px;">
          <input class="ace ace-switch ace-switch-7" data-name="ORDER_AUTO_CANCEL" type="checkbox" value="1" <?php echo is_checked($ORDER_AUTO_CANCEL, '1'); ?> onchange="toggleOption($(this))" />
          <span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
        </label>
        <input type="hidden" name="ORDER_AUTO_CANCEL" value="<?php echo $ORDER_AUTO_CANCEL; ?>" />
      </div>
      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <span class="help-block">เมื่อเปิดใช้งาน ระบบจะทำการยกเลิกออเดอร์ที่ติดเครดิตลิมิตแล้วไม่ได้รับการดำเนินการภายในเวลาที่กำหนดโดยอัตโนมัติ</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Order expiration</span></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <div class="input-group">
          <input type="number" class="form-control input-sm text-center" name="ORDER_EXPIRATION" value="<?php echo $ORDER_EXPIRATION; ?>" />
          <span class="input-group-addon">วัน</span>
        </div>
      </div>
      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <span class="help-block">ยกเลิกออเดอร์ที่ติดเครดิตลิมิตแล้วไม่ได้รับการดำเนินการภายในเวลาที่กำหนดโดยอัตโนมัติ</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Global Credit Limit</span></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <input type="number" class="form-control input-sm text-right" name="GLOBAL_CREDIT_LINE" id="credit-limit" value="<?php echo $GLOBAL_CREDIT_LINE; ?>" />
      </div>
      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <span class="help-block">จำนวนวงเงินเครดิตสูงสุดที่อนุญาตให้ลูกค้าไม่ประจำทุกคนสามารถสั่งซื้อได้</span>
      </div>

      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">แจ้งเตือน Submit Order</span></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
        <label style="padding-top:5px; margin-bottom:0px;">
          <input class="ace ace-switch ace-switch-7" data-name="WARNING_ORDER" type="checkbox" value="1" <?php echo is_checked($WARNING_ORDER, '1'); ?> onchange="toggleOption($(this))" />
          <span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
        </label>
        <input type="hidden" name="WARNING_ORDER" value="<?php echo $WARNING_ORDER; ?>" />
      </div>
      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <span class="help-block">เมื่อเปิดใช้งาน ระบบจะแจ้งเตือนเมื่อมีการ Submit Order</span>
      </div>
      <div class="divider-hidden"></div>


      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">ข้อความแจ้งเตือน</span></div>
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <textarea class="form-control input-sm" name="WARNING_ORDER_MESSAGE" id="warning-message" style="height:100px;"><?php echo $WARNING_ORDER_MESSAGE; ?></textarea>
      </div>
      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <span class="help-block">ข้อความแจ้งเตือนที่จะแสดงเมื่อมีการ Submit Order</span>
      </div>
      <div class="divider-hidden"></div>
      <div class="divider-hidden"></div>

      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <?php if ($this->pm->can_add or $this->pm->can_edit) : ?>
          <button type="button" class="btn btn-sm btn-success" onClick="updateConfig('orderForm')"><i class="fa fa-save"></i> บันทึก</button>
        <?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/row-->
  </form>