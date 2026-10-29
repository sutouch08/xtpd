<?php
$pm = get_permission('CLOSE_SYSTEM');
$cando = ($pm->can_add + $pm->can_edit) > 0 ? TRUE : FALSE;
?>

<form id="systemForm">
  <div class="row">
    <?php if ($cando === TRUE): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	
    ?>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
        <label class="fix-width-100">
          <input type="radio" class="ace" name="CLOSE_SYSTEM" value="0" <?php echo is_checked($CLOSE_SYSTEM, '0'); ?> />
          <span class="lbl"> เปิด</span>
        </label>

        <label class="fix-width-100">
          <input type="radio" class="ace" name="CLOSE_SYSTEM" value="1" <?php echo is_checked($CLOSE_SYSTEM, '1'); ?> />
          <span class="lbl"> ปิด</span>
        </label>

        <label class="fix-width-100">
          <input type="radio" class="ace" name="CLOSE_SYSTEM" value="2" <?php echo is_checked($CLOSE_SYSTEM, '2'); ?> />
          <span class="lbl"> ดูอย่างเดียว</span>
        </label>
      </div>
      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
      </div>
      <div class="divider-hidden"></div>
    <?php endif; ?>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Strong Password</span></div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
      <label style="padding-top:5px; margin-bottom:0px;">
        <input class="ace ace-switch ace-switch-7" data-name="USE_STRONG_PWD" type="checkbox" value="1" <?php echo is_checked($USE_STRONG_PWD, '1'); ?> onchange="toggleOption($(this))" />
        <span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
      </label>
      <input type="hidden" name="USE_STRONG_PWD" value="<?php echo $USE_STRONG_PWD; ?>" />
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">เมื่อเปิดใช้งาน การตั้งรหัสผ่านจะต้องมีความซับซ้อน โดยรหัสผ่านจะต้องมีความยาวไม่น้อยกว่า 8 ตัวอักษรและต้องประกอบด้วย ตัวอัษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และสัญลักษณ์พิเศษ อย่างน้อยอย่างละ 1 ตัว</span>
    </div>
    <div class="divider-hidden"></div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Discount Sales</span></div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
      <label style="padding-top:5px; margin-bottom:0px;">
        <input class="ace ace-switch ace-switch-7" data-name="USE_DISCSALE" type="checkbox" value="1" <?php echo is_checked($USE_DISCSALE, '1'); ?> onchange="toggleOption($(this))" />
        <span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
      </label>
      <input type="hidden" name="USE_DISCSALE" value="<?php echo $USE_DISCSALE; ?>" />
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">เมื่อเปิดใช้งาน การลดราคาขายจะถูกนำไปใช้</span>
    </div>
    <div class="divider-hidden"></div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Limit Display Lot.</span></div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-8">
      <input type="number" class="form-control input-sm input-small text-center" name="LIMIT_LOT_CHECK" value="<?php echo $LIMIT_LOT_CHECK; ?>" />
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">จำกัดการแสดงผล Lot. สินค้าไม่เกินจำนวนที่กำหนด (เก่าไปใหม่) 0 = ไม่จำกัด</span>
    </div>


    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Sync logs</span></div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-8">
      <div class="input-group">
        <input type="text" class="form-control input-sm input-small text-center" name="KEEP_SYNC_LOGS" value="<?php echo $KEEP_SYNC_LOGS; ?>" />
        <span class="input-group-addon"> วัน</span>
      </div>
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">เก็บ Sync logs ระหว่าง Web กับ SAP ไว้ไม่เกินจำนวนวันที่กำหนด</span>
    </div>

    <div class="divider"></div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">SMTP Email</span></div>
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
      <input type="email" class="form-control input-sm fix-width-250" id="smtp-email" name="SMTP_EMAIL" value="<?php echo $SMTP_EMAIL; ?>" />
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">อีเมลสำหรับส่งผ่าน SMTP</span>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">SMTP Password</span></div>
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
      <textarea class="form-control input-sm input-xxlarge" id="smtp-password" name="SMTP_PASSWORD"><?php echo $SMTP_PASSWORD; ?></textarea>
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">รหัสผ่าน หรือ App Password สำหรับส่งผ่าน SMTP</span>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">SMTP Server</span></div>
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
      <div class="input-group">
        <input type="text" class="form-control input-sm fix-width-250" id="smtp-server" name="SMTP_SERVER" value="<?php echo $SMTP_SERVER; ?>" />
        <span class="input-group-addon fix-width-100" style="border:none; background-color:#fff; text-align:right;">PORT</span>
        <input type="text" class="form-control input-sm fix-width-60 text-center" id="smtp-port" name="SMTP_PORT" value="<?php echo $SMTP_PORT; ?>" />
      </div>
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">เซิร์ฟเวอร์สำหรับส่ง email ผ่าน SMTP</span>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Receiver Email</span></div>
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
      <input type="email" class="form-control input-sm fix-width-250" id="receiver-email" name="RECEIVER_EMAIL" value="<?php echo $RECEIVER_EMAIL; ?>" />
    </div>
    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <span class="help-block">Email สำหรับผู้รับ</span>
    </div>

    <div class="divider-hidden"></div>
    <div class="divider-hidden"></div>

    <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
      <?php if ($this->pm->can_add or $this->pm->can_edit or $cando) : ?>
        <button type="button" class="btn btn-sm btn-success" onClick="updateConfig('systemForm')"><i class="fa fa-save"></i> บันทึก</button>
      <?php endif; ?>
    </div>
    <div class="divider-hidden"></div>

  </div><!--/row-->
</form>